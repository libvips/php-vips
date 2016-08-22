
#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_vips.h"

#include <vips/vips.h>

/* If you declare any globals in php_vips.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(vips)
*/

/* True global resources - no need for thread safety here */
static int le_vips;

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("vips.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_vips_globals, vips_globals)
    STD_PHP_INI_ENTRY("vips.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_vips_globals, vips_globals)
PHP_INI_END()
*/
/* }}} */

/* Remove the following function when you have successfully modified config.m4
   so that your module can be compiled into PHP, it exists only for testing
   purposes. */

/* Every user-visible function in PHP should document itself in the source */
/* {{{ proto string confirm_vips_compiled(string arg)
   Return a string to confirm that the module is compiled in */
PHP_FUNCTION(confirm_vips_compiled)
{
	char *arg = NULL;
	size_t arg_len, len;
	zend_string *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "s", &arg, &arg_len) == FAILURE) {
		return;
	}

	strg = strpprintf(0, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "vips", arg);

	RETURN_STR(strg);
}
/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and
   unfold functions in source code. See the corresponding marks just before
   function definition, where the functions purpose is also documented. Please
   follow this convention for the convenience of others editing your code.
*/

/* Output the vips error buffer as a docref.
 */
static void
error_vips( void )
{
	php_error_docref(NULL, E_WARNING, "%s", vips_error_buffer());
	vips_error_clear();
}

/* Track stuff during a call to a vips operation in one of these.
 */
typedef struct _VipsPhpCall {
	/* Parameters.
	 */
	const char *operation_name;
	const char *option_string;
	int argc;
	zval *argv;
	zval **out;

	/* The operation we are calling.
	 */
	VipsOperation *operation;

	/* The num of args this operation needs from php.
	 */
	int args_required;

	/* Extra php array of optional args.
	 */
	zval *options;

} VipsPhpCall;

static void
vips_php_call_free(VipsPhpCall *call)
{
	VIPS_UNREF(call->operation);
	g_free(call);
}

static VipsPhpCall *
vips_php_call_new(const char *operation_name,
	const char *option_string, int argc, zval *argv, zval **out)
{
	VipsPhpCall *call;

	call = g_new0( VipsPhpCall, 1 );
	call->operation_name = operation_name;
	call->option_string = option_string;
	call->argc = argc;
	call->argv = argv;
	call->out = out;

	if (!(call->operation = vips_operation_new(operation_name))) {
		vips_php_call_free(call);
		error_vips();
		return NULL;
	}

	return call;
}

/* Set a gvalue from a php value. pspec indicates the kind of thing the operation is expecting.
 */
static int
vips_php_to_gvalue(VipsPhpCall *call, GParamSpec *pspec, zval *value, GValue *gvalue)
{
}

static void *
vips_php_set_required_input(VipsObject *object, 
	GParamSpec *pspec, VipsArgumentClass *argument_class, VipsArgumentInstance *argument_instance, 
	void *a, void *b)
{
	VipsPhpCall *call = (VipsPhpCall *) a;

	if ((argument_class->flags & VIPS_ARGUMENT_REQUIRED) &&
		(argument_class->flags & VIPS_ARGUMENT_CONSTRUCT) &&
		(argument_class->flags & VIPS_ARGUMENT_INPUT) &&
		!(argument_class->flags & VIPS_ARGUMENT_DEPRECATED) &&
		!argument_instance->assigned) {
		if (call->args_required < call->argc) {
			const char *name = g_param_spec_get_name(pspec);
			int i = call->args_required;
			GValue gvalue = { 0 };

			if(vips_php_to_gvalue(call, pspec, &call->argv[i], &gvalue))
				return call;
			g_object_set_property(G_OBJECT(call->operation), name, &gvalue);
		}

		call->args_required += 1;
	}

	return NULL;
}

/* Call any vips operation, with the arguments coming from an array of zval. argv can have an extra final 
 * arg, which is an associative array of extra optional arguments. 
 */
static int
vips_php_call(const char *operation_name, const char *option_string, int argc, zval *argv, zval **out)
{
	VipsPhpCall *call;

	if (!(call = vips_php_call_new(operation_name, option_string, argc, argv, out))) {
		return -1;
	}

	/* Set str options before vargs options, so the user can't
	 * override things we set deliberately.
	 */
	if( option_string &&
		vips_object_set_from_string(VIPS_OBJECT(call->operation), option_string)) {
		error_vips();
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* Set all required input args from argv.
	 */
	if (vips_argument_map(VIPS_OBJECT(call->operation), vips_php_set_required_input, call, NULL)) {
		error_vips();
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* args_required must match argc, or we allow one extra final arg for options.
	 */
	if (call->argc == call->args_required + 1) {
		/* Make sure it really is an array.
		 */
		if (zend_parse_parameter(0, call->argc - 1, &call->argv[call->argc - 1], "a", &call->options) == FAILURE) {
			vips_object_unref_outputs(VIPS_OBJECT(call->operation));
			vips_php_call_free(call);
			return -1;
		}
	}
	else if (call->argc != call->args_required) {
		php_error_docref(NULL, E_WARNING, "operation expects %d arguments, but you supplied %d",
			call->args_required, call->argc);
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* Set all optional arguments.
	 */
	if (call->options) {
		zend_string *key;
		zval *value;

		ZEND_HASH_FOREACH_STR_KEY_VAL(Z_ARRVAL_P(call->options), key, value) {
			if (key == NULL) {
				continue;
			}

			if (strcmp("xxx", ZSTR_VAL(key)) == 0) {
			}
		} ZEND_HASH_FOREACH_END();
	}

	/* Look up in cache and build.
	 */
	if( vips_cache_operation_buildp(&call->operation)) {
		error_vips();
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* Walk args again, writing output.
	 */
	/*
	if (vips_argument_map(VIPS_OBJECT(call->operation), vips_php_write_required_output, call, NULL)) {
	}
	 */
	if(call->options) {
		/* Loop over options writing optional output.
		 */
	}

	vips_php_call_free(call);

	return 0;
}

/* Call any vips operation, with the args coming from php and the result going back to php. Ignore the
 * first @skip arguments from php. 
 */
static void
call_vips(INTERNAL_FUNCTION_PARAMETERS, const char *operation_name, int skip)
{
	int argc;
	zval *argv;
	zval *result;
	int i;

	argc = ZEND_NUM_ARGS();

	if (argc < 2) {
		WRONG_PARAM_COUNT;
	}

	argv = (zval *)emalloc(argc * sizeof(zval));

	if (zend_get_parameters_array_ex(argc, argv) == FAILURE) {
		efree(argv);
		WRONG_PARAM_COUNT;
	}

	result = NULL;
	if (vips_php_call(operation_name, "", argc - skip, argv + skip, &result)) {
		efree(argv);
		return;
	}

	/*
	array_init(return_value);

	for(i = 1; i < argc; i++) {
		zval_add_ref(argv[i]);
		add_index_zval(return_value, i, *argv[i]);
	}
	 */

	efree(argv);

	RETURN_ZVAL(result, 1, 0);
}

/* {{{ proto resource vips_image_new_from_file(string filename [, array options])
   Open an image from a filename */
PHP_FUNCTION(vips_image_new_from_file)
{
	char *filename;
	size_t filename_len;
	zval *options = NULL;
	VipsImage *image;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "p|a", &filename, &filename_len, &options) == FAILURE) {
		return;
	}

	/*
	call_vips(INTERNAL_FUNCTION_PARAM_PASSTHRU, 
	 */

	if (!(image = vips_image_new_from_file(filename, NULL))) {
		error_vips();
		RETURN_FALSE;
	}

	RETVAL_RES(zend_register_resource(image, le_vips));

}
/* }}} */

/* {{{ proto bool vips_image_write_to_file(resource image, string filename [, array options])
   Write an image to a filename */
PHP_FUNCTION(vips_image_write_to_file)
{
	zval *IM;
	char *filename;
	size_t filename_len;
	zval *options = NULL;
	VipsImage *image;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "rp|a", &IM, &filename, &filename_len, &options) == FAILURE) {
		return;
	}

	if ((image = (VipsImage *)zend_fetch_resource(Z_RES_P(IM), "VipsImage", le_vips)) == NULL) {
		RETURN_FALSE;
	}

	if (vips_image_write_to_file(image, filename, NULL)) {
		error_vips();
		RETURN_FALSE;
	}

	RETURN_TRUE;
}
/* }}} */

/* {{{ proto resource vips_invert(resource image [, array options])
   Photographic negative */
PHP_FUNCTION(vips_invert)
{
	zval *IM;
	VipsImage *image;
	zval *options = NULL;
	VipsImage *out;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "r|a", &IM, &options) == FAILURE) {
		return;
	}

	if ((image = (VipsImage *)zend_fetch_resource(Z_RES_P(IM), "VipsImage", le_vips)) == NULL) {
		RETURN_FALSE;
	}

	if (vips_invert(image, &out, NULL)) {
		error_vips();
		RETURN_FALSE;
	}

	RETVAL_RES(zend_register_resource(out, le_vips));
}
/* }}} */

/* {{{ php_vips_init_globals
 */
/* Uncomment this function if you have INI entries
static void php_vips_init_globals(zend_vips_globals *vips_globals)
{
	vips_globals->global_value = 0;
	vips_globals->global_string = NULL;
}
*/
/* }}} */

/* {{{ php_free_vips_object
 *  */
static void php_free_vips_object(zend_resource *rsrc)
{
	g_object_unref((GObject *) rsrc->ptr);
}
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(vips)
{
	/* If you have INI entries, uncomment these lines
	REGISTER_INI_ENTRIES();
	*/

	/* We're supposed to use the filename of something we think is in
	 * $VIPSHOME/bin, but we don't have that. Use a nonsense name and
	 * vips_init() will fall back to other techniques for finding data
	 * files.
	 */
	if( VIPS_INIT( "banana" ) )
		return FAILURE;

	le_vips = zend_register_list_destructors_ex(php_free_vips_object, NULL, "vips", module_number);

	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(vips)
{
	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/

	vips_shutdown();

	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request start */
/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(vips)
{
#if defined(COMPILE_DL_VIPS) && defined(ZTS)
	ZEND_TSRMLS_CACHE_UPDATE();
#endif
	return SUCCESS;
}
/* }}} */

/* Remove if there's nothing to do at request end */
/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(vips)
{
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(vips)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "vips support", "enabled");
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}
/* }}} */

ZEND_BEGIN_ARG_INFO(arginfo_vips_image_new_from_file, 0)
	ZEND_ARG_INFO(0, filename)
	ZEND_ARG_INFO(0, options)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_vips_image_write_to_file, 0)
	ZEND_ARG_INFO(0, image)
	ZEND_ARG_INFO(0, filename)
	ZEND_ARG_INFO(0, options)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_vips_invert, 0)
	ZEND_ARG_INFO(0, image)
	ZEND_ARG_INFO(0, options)
ZEND_END_ARG_INFO()

/* {{{ vips_functions[]
 *
 * Every user visible function must have an entry in vips_functions[].
 */
const zend_function_entry vips_functions[] = {
	PHP_FE(confirm_vips_compiled,	NULL)		/* For testing, remove later. */
	PHP_FE(vips_image_new_from_file, arginfo_vips_image_new_from_file)
	PHP_FE(vips_image_write_to_file, arginfo_vips_image_write_to_file)
	PHP_FE(vips_invert, arginfo_vips_invert)

	PHP_FE_END	/* Must be the last line in vips_functions[] */
};
/* }}} */

/* {{{ vips_module_entry
 */
zend_module_entry vips_module_entry = {
	STANDARD_MODULE_HEADER,
	"vips",
	vips_functions,
	PHP_MINIT(vips),
	PHP_MSHUTDOWN(vips),
	PHP_RINIT(vips),		/* Replace with NULL if there's nothing to do at request start */
	PHP_RSHUTDOWN(vips),	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(vips),
	PHP_VIPS_VERSION,
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_VIPS
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(vips)
#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
