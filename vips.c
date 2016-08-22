/* Uncomment for some logging.
 */
#define VIPS_DEBUG

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_vips.h"

#include <vips/vips.h>
#include <vips/debug.h>

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
	VIPS_DEBUG_MSG("error_vips: %s\n", vips_error_buffer());

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
	VIPS_DEBUG_MSG("vips_php_call_free:\n");

	VIPS_UNREF(call->operation);
	g_free(call);
}

static VipsPhpCall *
vips_php_call_new(const char *operation_name,
	const char *option_string, int argc, zval *argv, zval **out)
{
	VipsPhpCall *call;

	VIPS_DEBUG_MSG("vips_php_call_new: %s\n", operation_name );
	VIPS_DEBUG_MSG("    option_string = \"%s\", argc = %d\n", option_string, argc);

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
vips_php_to_gvalue(VipsPhpCall *call, GParamSpec *pspec, zval *zvalue, GValue *gvalue)
{
	GType pspec_type = G_PARAM_SPEC_VALUE_TYPE(pspec);

	/* We can't use a switch since some param specs don't have fundamental
	 * types and are hence not compile-time constants, argh.
	 */
	if (G_IS_PARAM_SPEC_STRING(pspec)) {
		/* These are GStrings, vips refstrings are handled by boxed, below.
		 */
		convert_to_string_ex(zvalue);
		g_value_init(gvalue, pspec_type);
		g_value_set_string(gvalue, Z_STRVAL_P(zvalue));
	}
	else if (G_IS_PARAM_SPEC_OBJECT(pspec)) {
		VipsImage *image;

		if (Z_TYPE_P(zvalue) != IS_RESOURCE ||
			(image = (VipsImage *)zend_fetch_resource(Z_RES_P(zvalue), "VipsImage", le_vips)) == NULL) {
			php_error_docref(NULL, E_WARNING, "not a VipsImage");
			return -1;
		}

		g_value_init(gvalue, pspec_type);
		g_value_set_object(gvalue, image);
	}
	else if (G_IS_PARAM_SPEC_INT(pspec)) {
		convert_to_long_ex(zvalue);
		g_value_init(gvalue, pspec_type);
		g_value_set_int(gvalue, Z_LVAL_P(zvalue));
	}
	else if (G_IS_PARAM_SPEC_UINT64(pspec)) {
		convert_to_long_ex(zvalue);
		g_value_init(gvalue, pspec_type);
		g_value_set_uint64(gvalue, Z_LVAL_P(zvalue));
	}
	else if (G_IS_PARAM_SPEC_BOOLEAN(pspec)) {
		convert_to_boolean(zvalue);
		g_value_init(gvalue, pspec_type);
		g_value_set_boolean(gvalue, Z_LVAL_P(zvalue));
	}
	else if (G_IS_PARAM_SPEC_ENUM(pspec)) {
		int enum_value;

		convert_to_string_ex(zvalue);
		if ((enum_value = vips_enum_from_nick("enum", pspec_type, Z_STRVAL_P(zvalue))) < 0 ) {
			error_vips();
			return -1;
		}
		g_value_init(gvalue, pspec_type);
		g_value_set_enum(gvalue, enum_value);
	}
	else if (G_IS_PARAM_SPEC_FLAGS(pspec)) {
		convert_to_long_ex(zvalue);
		g_value_init(gvalue, pspec_type);
		g_value_set_flags(gvalue, Z_LVAL_P(zvalue));
	}
	else if (G_IS_PARAM_SPEC_DOUBLE(pspec)) {
		convert_to_double_ex(zvalue);
		g_value_init(gvalue, pspec_type);
		g_value_set_double(gvalue, Z_DVAL_P(zvalue));
	}
	else {
		/* Need to add at least G_IS_PARAM_SPEC_BOXED(pspec) I guess.
		 */

		g_warning( "%s: .%s unimplemented property type %s",
			G_STRLOC,
			g_param_spec_get_name(pspec),
			g_type_name(pspec_type) );
	}

	return 0;
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

			if (vips_php_to_gvalue(call, pspec, &call->argv[i], &gvalue))
				return call;

#ifdef VIPS_DEBUG
{
			char *str_value;

			str_value = g_strdup_value_contents(&gvalue);
			VIPS_DEBUG_MSG("   %s.%s = %s\n", call->operation_name, name, str_value);
			g_free(str_value);
}
#endif/*VIPS_DEBUG*/

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
vips_php_call_array(const char *operation_name, const char *option_string, int argc, zval *argv, zval **out)
{
	VipsPhpCall *call;

	VIPS_DEBUG_MSG("vips_php_call_array:\n");

	if (!(call = vips_php_call_new(operation_name, option_string, argc, argv, out))) {
		return -1;
	}

	/* Set str options before vargs options, so the user can't
	 * override things we set deliberately.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: setting args from option_string ...\n");
	if( option_string &&
		vips_object_set_from_string(VIPS_OBJECT(call->operation), option_string)) {
		error_vips();
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* Set all required input args from argv.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: setting required input args ...\n");
	if (vips_argument_map(VIPS_OBJECT(call->operation), vips_php_set_required_input, call, NULL)) {
		error_vips();
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* args_required must match argc, or we allow one extra final arg for options.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: testing argc ...\n");
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
	VIPS_DEBUG_MSG("vips_php_call_array: setting optional input args ...\n");
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
	VIPS_DEBUG_MSG("vips_php_call_array: building ...\n");
	if( vips_cache_operation_buildp(&call->operation)) {
		error_vips();
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* Walk args again, writing output.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: writing required output ...\n");
	/*
	if (vips_argument_map(VIPS_OBJECT(call->operation), vips_php_write_required_output, call, NULL)) {
	}
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: writing optional output ...\n");
	if(call->options) {
		/* Loop over options writing optional output.
		 */
	}

	vips_php_call_free(call);

	VIPS_DEBUG_MSG("vips_php_call_array: done!\n");

	return 0;
}

/* {{{ proto value vips_php_call(string operation_name [, more])
   Call any vips operation */
PHP_FUNCTION(vips_php_call)
{
	int argc;
	zval *argv;
	char *operation_name;
	size_t operation_name_len;
	zval *result;

	VIPS_DEBUG_MSG("vips_php_call:\n");

	argc = ZEND_NUM_ARGS();

	if (argc < 1) {
		WRONG_PARAM_COUNT;
	}

	argv = (zval *)emalloc(argc * sizeof(zval));

	if (zend_get_parameters_array_ex(argc, argv) == FAILURE) {
		efree(argv);
		WRONG_PARAM_COUNT;
	}

	if(zend_parse_parameter(0, 0, &argv[0], "s", &operation_name, &operation_name_len) == FAILURE) {
		efree(argv);
		return;
	}

	result = NULL;
	if (vips_php_call_array(operation_name, "", argc - 1, argv + 1, &result)) {
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
/* }}} */

/* {{{ proto resource vips_image_new_from_file(string filename [, array options])
   Open an image from a filename */
PHP_FUNCTION(vips_image_new_from_file)
{
	char *filename;
	size_t filename_len;
	zval *options = NULL;
	VipsImage *image;

	VIPS_DEBUG_MSG("vips_image_new_from_file:\n");

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "p|a", &filename, &filename_len, &options) == FAILURE) {
		return;
	}
	VIPS_DEBUG_MSG("vips_image_new_from_file: filename = %s\n", filename);

	/*
	call_vips(INTERNAL_FUNCTION_PARAM_PASSTHRU, 
	 */

	if (!(image = vips_image_new_from_file(filename, NULL))) {
		error_vips();
		RETURN_FALSE;
	}

	VIPS_DEBUG_MSG("vips_image_new_from_file: image = %p\n", image);

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
	zval *options = NULL;
	VipsImage *image;
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
	VIPS_DEBUG_MSG("php_free_vips_object: %p\n", rsrc->ptr);

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

ZEND_BEGIN_ARG_INFO(arginfo_vips_php_call, 0)
	ZEND_ARG_INFO(0, operation_name)
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
	PHP_FE(vips_php_call, arginfo_vips_php_call)

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
