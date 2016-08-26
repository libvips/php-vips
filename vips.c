/* Uncomment for some logging.
#define VIPS_DEBUG
 */

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
static int le_gobject;

/* {{{ PHP_INI
 */
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
    STD_PHP_INI_ENTRY("vips.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_vips_globals, vips_globals)
    STD_PHP_INI_ENTRY("vips.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_vips_globals, vips_globals)
PHP_INI_END()
*/
/* }}} */

/* Output the vips error buffer as a docref.
 */
static void
error_vips( void )
{
	VIPS_DEBUG_MSG("error_vips: %s\n", vips_error_buffer());

	php_error_docref(NULL, E_WARNING, "%s", vips_error_buffer());
	vips_error_clear();
}

/* {{{ proto static int vips_php_call_array(const char *operation_name, zval *instance, const char *option_string, int argc, zval *argv, zval *return_value)
   Call any vips operation. */

/* Track stuff during a call to a vips operation in one of these.
 */
typedef struct _VipsPhpCall {
	/* Parameters.
	 */
	const char *operation_name;
	zval *instance;
	const char *option_string;
	int argc;
	zval *argv;

	/* The operation we are calling.
	 */
	VipsOperation *operation;

	/* The num of args this operation needs from php. This does not include the
	 * @instance zval.
	 */
	int args_required;

	/* If we've already used the instance zval.
	 */
	gboolean used_instance;

	/* Extra php array of optional args.
	 */
	zval *options;

	/* The first image arg ... the thing we expand constants to match.
	 */
	VipsImage *match_image;

} VipsPhpCall;

static void
vips_php_call_free(VipsPhpCall *call)
{
	VIPS_DEBUG_MSG("vips_php_call_free:\n");

	VIPS_UNREF(call->operation);
	g_free(call);
}

static VipsPhpCall *
vips_php_call_new(const char *operation_name, zval *instance, 
	const char *option_string, int argc, zval *argv)
{
	VipsPhpCall *call;

	VIPS_DEBUG_MSG("vips_php_call_new: %s\n", operation_name );
	VIPS_DEBUG_MSG("    option_string = \"%s\", argc = %d\n", 
			option_string, argc);

	call = g_new0( VipsPhpCall, 1 );
	call->operation_name = operation_name;
	call->instance = instance;
	call->option_string = option_string;
	call->argc = argc;
	call->argv = argv;

	if (!(call->operation = vips_operation_new(operation_name))) {
		vips_php_call_free(call);
		error_vips();
		return NULL;
	}

	return call;
}

static void
vips_php_analyze_arg(VipsPhpCall *call, zval *arg)
{
	if (Z_TYPE_P(arg) == IS_ARRAY) {
		const int n = zend_hash_num_elements(Z_ARRVAL_P(arg));

		int i;

		for (i = 0; i < n; i++) { 
			zval *item = zend_hash_index_find(Z_ARRVAL_P(arg), i);

			if (item) {
				vips_php_analyze_arg(call, item);
			}
		}
	}
	else if (Z_TYPE_P(arg) == IS_RESOURCE) {
		VipsImage *image;

		if( (image = (VipsImage *)zend_fetch_resource(Z_RES_P(arg), 
			"GObject", le_gobject)) != NULL) {
			if (!call->match_image) {
				call->match_image = image;
			}
		}
	}
}

static int
vips_php_blob_free(void *buf, void *area)
{
	g_free(buf);

	return 0;
}

/* Expand a constant (eg. 12, "12" or [1, 2, 3]) into an image using 
 * @match_image as a guide.
 */
static VipsImage *
expand_constant(VipsImage *match_image, zval *constant)
{
	VipsImage *result;
	VipsImage *x;

	if (vips_black(&result, 1, 1, NULL)) {
		error_vips();
		return NULL;
	}

	if (Z_TYPE_P(constant) == IS_ARRAY) {
		const int n = zend_hash_num_elements(Z_ARRVAL_P(constant));

		double *ones;
		double *offsets;
		int i;

		ones = VIPS_ARRAY(result, n, double);
		offsets = VIPS_ARRAY(result, n, double);

		for (i = 0; i < n; i++) {
			zval *ele;

			ones[i] = 1.0;

			if ((ele = zend_hash_index_find(Z_ARRVAL_P(constant), i)) != NULL) {
				convert_to_double_ex(ele);
				offsets[i] = zval_get_double(ele);
			}
		}

		if (vips_linear(result, &x, ones, offsets, n, NULL)) {
			error_vips();
			return NULL;
		}
		g_object_unref(result);
		result = x;
	}
	else {
		convert_to_double_ex(constant);

		if (vips_linear1(result, &x, 1.0, zval_get_double(constant), NULL)) {
			error_vips();
			return NULL;
		}
		g_object_unref(result);
		result = x;
	}

	if (vips_cast(result, &x, match_image->BandFmt, NULL)) {
		error_vips();
		return NULL;
	}
	g_object_unref(result);
	result = x;

	if (vips_embed(result, &x, 0, 0, match_image->Xsize, match_image->Ysize, 
		"extend", VIPS_EXTEND_COPY,
		NULL)) {
		error_vips();
		return NULL;
	}
	g_object_unref(result);
	result = x;

	result->Type = match_image->Type;
	result->Xres = match_image->Xres;
	result->Yres = match_image->Yres;
	result->Xoffset = match_image->Xoffset;
	result->Yoffset = match_image->Yoffset;

	return result;
}

/* Is a zval a rectangular 2D array.
 */
static gboolean
is_2D(zval *array)
{
	int height;
	zval *row;
	int width;
	int y;

	if (Z_TYPE_P(array) != IS_ARRAY) {
		return FALSE;
	}

	height = zend_hash_num_elements(Z_ARRVAL_P(array));
	if ((row = zend_hash_index_find(Z_ARRVAL_P(array), 0)) == NULL ||
		Z_TYPE_P(row) != IS_ARRAY) { 
		return FALSE;
	}
	width = zend_hash_num_elements(Z_ARRVAL_P(row));

	for (y = 1; y < height; y++) {
		if ((row = zend_hash_index_find(Z_ARRVAL_P(array), y)) == NULL ||
			Z_TYPE_P(row) != IS_ARRAY ||
			zend_hash_num_elements(Z_ARRVAL_P(row)) != width) {
			return FALSE;
		}
	}

	return TRUE;
}

/* Make a vips matrix image from a 2D zval. @array must have passed is_2D()
 * before calling this.
 */
static VipsImage *
matrix_from_zval(zval *array)
{
	int width;
	int height;
	zval *row;
	VipsImage *mat;
	int x, y;

	height = zend_hash_num_elements(Z_ARRVAL_P(array));
	row = zend_hash_index_find(Z_ARRVAL_P(array), 0);
	g_assert(Z_TYPE_P(row) == IS_ARRAY);
	width = zend_hash_num_elements(Z_ARRVAL_P(row));
	mat = vips_image_new_matrix(width, height);

	for (y = 0; y < height; y++) {
		row = zend_hash_index_find(Z_ARRVAL_P(array), y);
		g_assert(Z_TYPE_P(row) == IS_ARRAY);
		g_assert(zend_hash_num_elements(Z_ARRVAL_P(row)) == width);

		for (x = 0; x < width; x++) {
			zval *ele;

			ele = zend_hash_index_find(Z_ARRVAL_P(row), x);
			convert_to_double_ex(ele);
			*VIPS_MATRIX(mat, x, y) = zval_get_double(ele);
		}
	}

	return mat;
}

/* Turn a zval into an image. A 2D array of numbers becomes a matrix image, a
 * 1D array or a simple constant is expanded to match @match_image.
 */
static VipsImage *
imageize(VipsImage *match_image, zval *constant)
{
	if (is_2D(constant)) {
		return matrix_from_zval(constant);
	}
	else {
		return expand_constant(match_image, constant);
	}
}

/* Set a gvalue from a php value. 
 *
 * Set the type of the gvalue before calling this to hint what kind of gvalue 
 * to make. For example if type is an enum, a zval string will be used to 
 * look up the enum nick.
 *
 * If non-NULL, @match_image is used to turn constants into images. 
 */
static int
vips_php_zval_to_gval(VipsImage *match_image, zval *zvalue, GValue *gvalue)
{
	GType type = G_VALUE_TYPE(gvalue);

	/* The fundamental type ... eg. G_TYPE_ENUM for a VIPS_TYPE_KERNEL, or
	 * G_TYPE_OBJECT for VIPS_TYPE_IMAGE().
	 */
	GType fundamental = G_TYPE_FUNDAMENTAL(type);

	VipsImage *image;
	int enum_value;

	switch (fundamental) {
		case G_TYPE_STRING:
			/* These are GStrings, vips refstrings are handled by boxed, see 
			 * below.
			 */
			convert_to_string_ex(zvalue);
			g_value_set_string(gvalue, Z_STRVAL_P(zvalue));
			break;

		case G_TYPE_OBJECT:
			if (Z_TYPE_P(zvalue) == IS_RESOURCE) {
			   if ((image = (VipsImage *)zend_fetch_resource(Z_RES_P(zvalue), 
					"GObject", le_gobject)) == NULL) {
					php_error_docref(NULL, E_WARNING, "not a VipsImage");
					return -1;
				}
			}
			else {
				if (!(image = imageize(match_image, zvalue))) {
					return -1;
				}
			}

			g_value_set_object(gvalue, image);
			break;

		case G_TYPE_INT:
			convert_to_long_ex(zvalue);
			g_value_set_int(gvalue, Z_LVAL_P(zvalue));
			break;

		case G_TYPE_UINT64:
			convert_to_long_ex(zvalue);
			g_value_set_uint64(gvalue, Z_LVAL_P(zvalue));
			break;

		case G_TYPE_BOOLEAN:
			convert_to_boolean(zvalue);
			g_value_set_boolean(gvalue, Z_LVAL_P(zvalue));
			break;

		case G_TYPE_ENUM:
			if (Z_TYPE_P(zvalue) == IS_LONG) {
				enum_value = Z_LVAL_P(zvalue);
			}
			else {
				convert_to_string_ex(zvalue);
				if ((enum_value = vips_enum_from_nick("enum", 
					type, Z_STRVAL_P(zvalue))) < 0 ) {
					error_vips();
					return -1;
				}
			}
			g_value_set_enum(gvalue, enum_value);
			break;

		case G_TYPE_FLAGS:
			convert_to_long_ex(zvalue);
			g_value_set_flags(gvalue, Z_LVAL_P(zvalue));
			break;

		case G_TYPE_DOUBLE:
			convert_to_double_ex(zvalue);
			g_value_set_double(gvalue, Z_DVAL_P(zvalue));
			break;

		case G_TYPE_BOXED:
			if (type == VIPS_TYPE_REF_STRING) {
				convert_to_string_ex(zvalue);
				vips_value_set_ref_string(gvalue, Z_STRVAL_P(zvalue));
			}
			else if (type == VIPS_TYPE_BLOB) {
				void *buf;

				convert_to_string_ex(zvalue);
				buf = g_malloc(Z_STRLEN_P(zvalue));
				memcpy(buf, Z_STRVAL_P(zvalue), Z_STRLEN_P(zvalue));

				vips_value_set_blob(gvalue, 
					vips_php_blob_free, buf, Z_STRLEN_P(zvalue));
			}
			else if (type == VIPS_TYPE_ARRAY_INT) {
				int *arr;
				int n;
				int i;

				convert_to_array_ex(zvalue);
				n = zend_hash_num_elements(Z_ARRVAL_P(zvalue));
				vips_value_set_array_int(gvalue, NULL, n);
				arr = vips_value_get_array_int(gvalue, NULL);
				for (i = 0; i < n; i++) {
					zval *ele;

					if ((ele = 
						zend_hash_index_find(Z_ARRVAL_P(zvalue), i)) != NULL) {
						convert_to_long_ex(ele);
						arr[i] = zval_get_long(ele);
					}
				}
			}
			else if (type == VIPS_TYPE_ARRAY_DOUBLE) {
				double *arr;
				int n;
				int i;

				convert_to_array_ex(zvalue);
				n = zend_hash_num_elements(Z_ARRVAL_P(zvalue));
				vips_value_set_array_double(gvalue, NULL, n);
				arr = vips_value_get_array_double(gvalue, NULL);
				for (i = 0; i < n; i++) {
					zval *ele;

					if ((ele = 
						zend_hash_index_find(Z_ARRVAL_P(zvalue), i)) != NULL) {
						convert_to_double_ex(ele);
						arr[i] = zval_get_double(ele);
					}
				}
			}
			else if (type == VIPS_TYPE_ARRAY_IMAGE) {
				VipsImage **arr;
				int n;
				int i;

				convert_to_array_ex(zvalue);
				n = zend_hash_num_elements(Z_ARRVAL_P(zvalue));
				vips_value_set_array_image(gvalue, n);
				arr = vips_value_get_array_image(gvalue, NULL);
				for (i = 0; i < n; i++) {
					zval *ele;
					VipsImage *image;

					if ((ele = 
						zend_hash_index_find(Z_ARRVAL_P(zvalue), i)) != NULL &&
						(image = (VipsImage *) zend_fetch_resource(Z_RES_P(ele),
							"GObject", le_gobject)) != NULL) {
						arr[i] = image;
						g_object_ref(image);
					}
				}
			}
			else {
				g_warning( "%s: unimplemented boxed type %s", 
					G_STRLOC, g_type_name(type) );
			}
			break;

		default:
			g_warning( "%s: unimplemented GType %s", 
				G_STRLOC, g_type_name(fundamental) );
			break;
	}

	return 0;
}

static int
vips_php_set_value(VipsPhpCall *call, GParamSpec *pspec, zval *zvalue)
{
	const char *name = g_param_spec_get_name(pspec);
	GType pspec_type = G_PARAM_SPEC_VALUE_TYPE(pspec);
	GValue gvalue = { 0 };

	g_value_init(&gvalue, pspec_type);
	if (vips_php_zval_to_gval(call->match_image, zvalue, &gvalue)) {
		g_value_unset(&gvalue);
		return -1;
	}

#ifdef VIPS_DEBUG
{
	char *str_value;

	str_value = g_strdup_value_contents(&gvalue);
	VIPS_DEBUG_MSG("    %s.%s = %s\n", call->operation_name, name, str_value);
	g_free(str_value);
}
#endif/*VIPS_DEBUG*/

	g_object_set_property(G_OBJECT(call->operation), name, &gvalue);
	g_value_unset(&gvalue);

	return 0;
}

static void *
vips_php_set_required_input(VipsObject *object, 
	GParamSpec *pspec, VipsArgumentClass *argument_class, 
	VipsArgumentInstance *argument_instance, 
	void *a, void *b)
{
	VipsPhpCall *call = (VipsPhpCall *) a;

	if ((argument_class->flags & VIPS_ARGUMENT_REQUIRED) &&
		(argument_class->flags & VIPS_ARGUMENT_CONSTRUCT) &&
		(argument_class->flags & VIPS_ARGUMENT_INPUT) &&
		!(argument_class->flags & VIPS_ARGUMENT_DEPRECATED) &&
		!argument_instance->assigned) {
		zval *arg;

		/* If this arg needs an image, we use instance, if we can.
		 */
		arg = NULL;
		if (G_PARAM_SPEC_VALUE_TYPE(pspec) == VIPS_TYPE_IMAGE &&
			call->instance &&
			!call->used_instance) {
			arg = call->instance;
			call->used_instance = TRUE;
		}
		else if (call->args_required < call->argc) {
			/* Pick the next zval off the supplied arg list.
			 */
			arg = &call->argv[call->args_required];
			call->args_required += 1;
		}
				
		if (arg &&
			vips_php_set_value(call, pspec, arg)) {
			return call;
		}
	}

	return NULL;
}

/* Set all optional arguments.
 */
static int
vips_php_set_optional_input(VipsPhpCall *call, zval *options)
{
	zend_string *key;
	zval *value;

	VIPS_DEBUG_MSG("vips_php_set_optional_input:\n");

	ZEND_HASH_FOREACH_STR_KEY_VAL(Z_ARRVAL_P(call->options), key, value) {
		char *name;
		GParamSpec *pspec;
		VipsArgumentClass *argument_class;
		VipsArgumentInstance *argument_instance;

		if (key == NULL) {
			continue;
		}

		name = ZSTR_VAL(key);
		if (vips_object_get_argument(VIPS_OBJECT(call->operation), name,
			&pspec, &argument_class, &argument_instance)) {
			error_vips();
			return -1;
		}

		if (!(argument_class->flags & VIPS_ARGUMENT_REQUIRED) &&
			(argument_class->flags & VIPS_ARGUMENT_INPUT) &&
			!(argument_class->flags & VIPS_ARGUMENT_DEPRECATED) &&
			vips_php_set_value(call, pspec, value)) {
			error_vips();
			return -1;
		}
	} ZEND_HASH_FOREACH_END();

	return 0;
}

/* Set a php zval from a gvalue. 
 */
static int
vips_php_gval_to_zval(GValue *gvalue, zval *zvalue)
{
	GType type = G_VALUE_TYPE(gvalue);

	/* The fundamental type ... eg. G_TYPE_ENUM for a VIPS_TYPE_KERNEL, or
	 * G_TYPE_OBJECT for VIPS_TYPE_IMAGE().
	 */
	GType fundamental = G_TYPE_FUNDAMENTAL(type);

	const char *str;
	GObject *gobject;
	zend_resource *resource;
	int enum_value;

	switch (fundamental) {
		case G_TYPE_STRING:
			/* These are GStrings, vips refstrings are handled by boxed, see 
			 * below.
			 */
			str = g_value_get_string(gvalue);
			ZVAL_STRING(zvalue, str);
			break;

		case G_TYPE_OBJECT:
			gobject = g_value_get_object(gvalue);
			resource = zend_register_resource(gobject, le_gobject);
			ZVAL_RES(zvalue, resource);
			break;

		case G_TYPE_INT:
			ZVAL_LONG(zvalue, g_value_get_int(gvalue));
			break;

		case G_TYPE_UINT64:
			ZVAL_LONG(zvalue, g_value_get_uint64(gvalue));
			break;

		case G_TYPE_BOOLEAN:
			ZVAL_LONG(zvalue, g_value_get_boolean(gvalue));
			break;

		case G_TYPE_ENUM:
			enum_value = g_value_get_enum(gvalue);
			str = vips_enum_nick(type, enum_value);
			ZVAL_STRING(zvalue, str);
			break;

		case G_TYPE_FLAGS:
			ZVAL_LONG(zvalue, g_value_get_flags(gvalue));
			break;

		case G_TYPE_DOUBLE:
			ZVAL_DOUBLE(zvalue, g_value_get_double(gvalue));
			break;

		case G_TYPE_BOXED:
			if (type == VIPS_TYPE_REF_STRING ||
				type == VIPS_TYPE_BLOB) {
				const char *str;
				size_t str_len;

				str = vips_value_get_ref_string(gvalue, &str_len);
				ZVAL_STRINGL(zvalue, str, str_len);
			}
			else if (type == VIPS_TYPE_ARRAY_DOUBLE) {
				double *arr;
				int n;
				int i;

				arr = vips_value_get_array_double(gvalue, &n);
				array_init(zvalue);
				for (i = 0; i < n; i++) {
					add_next_index_double(zvalue, arr[i]);
				}
			}
			else if (type == VIPS_TYPE_ARRAY_INT) {
				int *arr;
				int n;
				int i;

				arr = vips_value_get_array_int(gvalue, &n);
				array_init(zvalue);
				for (i = 0; i < n; i++) {
					add_next_index_long(zvalue, arr[i]);
				}
			}
			else if (type == VIPS_TYPE_ARRAY_IMAGE) {
				VipsImage **arr;
				int n;
				int i;

				arr = vips_value_get_array_image(gvalue, &n);
				array_init(zvalue);
				for (i = 0; i < n; i++) {
					zval x;

					g_object_ref(arr[i]);
					resource = zend_register_resource(arr[i], le_gobject);
					ZVAL_RES(&x, resource);
					add_next_index_zval(zvalue, &x);
				}
			}
			else {
				g_warning( "%s: unimplemented boxed type %s", 
					G_STRLOC, g_type_name(type));
			}
			break;

		default:
			g_warning( "%s: unimplemented GType %s", 
				G_STRLOC, g_type_name(fundamental));
			break;
	}

	return 0;
}

static int
vips_php_get_value(VipsPhpCall *call, GParamSpec *pspec, zval *zvalue)
{
	const char *name = g_param_spec_get_name(pspec);
	GType pspec_type = G_PARAM_SPEC_VALUE_TYPE(pspec);
	GValue gvalue = { 0 }; 

	g_value_init(&gvalue, pspec_type);
	g_object_get_property(G_OBJECT(call->operation), name, &gvalue);
	if (vips_php_gval_to_zval(&gvalue, zvalue)) {
		g_value_unset(&gvalue);
		return -1;
	}

#ifdef VIPS_DEBUG
{
	char *str_value;

	str_value = g_strdup_value_contents(&gvalue);
	VIPS_DEBUG_MSG("    %s.%s = %s\n", call->operation_name, name, str_value);
	g_free(str_value);
}
#endif/*VIPS_DEBUG*/

	g_value_unset(&gvalue);

	return 0;
}

static void *
vips_php_get_required_output(VipsObject *object, 
	GParamSpec *pspec, VipsArgumentClass *argument_class, 
	VipsArgumentInstance *argument_instance, 
	void *a, void *b)
{
	VipsPhpCall *call = (VipsPhpCall *) a;
	zval *return_value = (zval *) b;

	if ((argument_class->flags & VIPS_ARGUMENT_REQUIRED) &&
		(argument_class->flags & VIPS_ARGUMENT_OUTPUT) &&
		!(argument_class->flags & VIPS_ARGUMENT_DEPRECATED)) { 
		const char *name = g_param_spec_get_name(pspec);
		zval zvalue;

		if (vips_php_get_value(call, pspec, &zvalue)) {
			return call;
		}
		add_assoc_zval(return_value, name, &zvalue);
	}

	return NULL;
}

static int
vips_php_get_optional_output(VipsPhpCall *call, zval *options, 
	zval *return_value)
{
	zend_string *key;
	zval *value;

	VIPS_DEBUG_MSG("vips_php_get_optional_output:\n");

	ZEND_HASH_FOREACH_STR_KEY_VAL(Z_ARRVAL_P(call->options), key, value) {
		char *name;
		GParamSpec *pspec;
		VipsArgumentClass *argument_class;
		VipsArgumentInstance *argument_instance;

		if (key == NULL) {
			continue;
		}

		name = ZSTR_VAL(key);
		if (vips_object_get_argument(VIPS_OBJECT(call->operation), name,
			&pspec, &argument_class, &argument_instance)) {
			error_vips();
			return -1;
		}

		if (!(argument_class->flags & VIPS_ARGUMENT_REQUIRED) &&
			(argument_class->flags & VIPS_ARGUMENT_OUTPUT) &&
			!(argument_class->flags & VIPS_ARGUMENT_DEPRECATED)) {
			zval zvalue;

			if (vips_php_get_value(call, pspec, &zvalue)) {
				error_vips();
				return -1;
			}

			add_assoc_zval(return_value, name, &zvalue);
		}
	} ZEND_HASH_FOREACH_END();

	return 0;
}

/* Call any vips operation, with the arguments coming from an array of zval. 
 * argv can have an extra final arg, which is an associative array of 
 * optional arguments. 
 */
static int
vips_php_call_array(const char *operation_name, zval *instance, 
	const char *option_string, int argc, zval *argv, zval *return_value)
{
	VipsPhpCall *call;
	int i;

	VIPS_DEBUG_MSG("vips_php_call_array:\n");

	if (!(call = vips_php_call_new(operation_name, instance, option_string,
		argc, argv))) {
		return -1;
	}

	/* Some initial analysis of our args. Loop over them all, including the
	 * special 'instance' arg.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: analyzing input args ...\n");
	if (call->instance) {
		vips_php_analyze_arg(call, call->instance);
	}
	for (i = 0; i < argc; i++) {
		vips_php_analyze_arg(call, &call->argv[i]);
	}

	/* Set str options before vargs options, so the user can't
	 * override things we set deliberately.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: setting args from option_string ...\n");
	if (option_string &&
		vips_object_set_from_string(VIPS_OBJECT(call->operation), 
			option_string)) {
		error_vips();
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* Set all required input args from argv.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: setting required input args ...\n");
	if (vips_argument_map(VIPS_OBJECT(call->operation), 
		vips_php_set_required_input, call, NULL)) {
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
		if (zend_parse_parameter(0, call->argc - 1, &call->argv[call->argc - 1],
			"a", &call->options) == FAILURE) {
			vips_object_unref_outputs(VIPS_OBJECT(call->operation));
			vips_php_call_free(call);
			return -1;
		}
	}
	else if (call->argc != call->args_required) {
		php_error_docref(NULL, E_WARNING, 
			"operation expects %d arguments, but you supplied %d",
			call->args_required, call->argc);
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* Set all optional arguments.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: setting optional input args ...\n");
	if (call->options &&
		vips_php_set_optional_input(call, call->options)) {
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* Look up in cache and build.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: building ...\n");
	if (vips_cache_operation_buildp(&call->operation)) {
		error_vips();
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* Walk args again, getting required output.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: getting required output ...\n");
	array_init(return_value);
	if (vips_argument_map(VIPS_OBJECT(call->operation), 
		vips_php_get_required_output, call, return_value)) {
		error_vips();
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	/* And optional output.
	 */
	VIPS_DEBUG_MSG("vips_php_call_array: getting optional output ...\n");
	if (call->options &&
		vips_php_get_optional_output(call, call->options, return_value)) {
		vips_object_unref_outputs(VIPS_OBJECT(call->operation));
		vips_php_call_free(call);
		return -1;
	}

	vips_php_call_free(call);

	VIPS_DEBUG_MSG("vips_php_call_array: done!\n");

	return 0;
}

/* }}} */

/* {{{ proto mixed vips_php_call(string operation_name, resource instance [, more])
   Call any vips operation */
PHP_FUNCTION(vips_call)
{
	int argc;
	zval *argv;
	char *operation_name;
	size_t operation_name_len;
	zval *instance;

	VIPS_DEBUG_MSG("vips_call:\n");

	argc = ZEND_NUM_ARGS();

	if (argc < 1) {
		WRONG_PARAM_COUNT;
	}

	argv = (zval *)emalloc(argc * sizeof(zval));

	if (zend_get_parameters_array_ex(argc, argv) == FAILURE) {
		efree(argv);
		WRONG_PARAM_COUNT;
	}

	if (zend_parse_parameter(0, 0, &argv[0], 
		"s", &operation_name, &operation_name_len) == FAILURE) {
		efree(argv);
		return;
	}

	if (zend_parse_parameter(0, 1, &argv[1], "r!", &instance) == FAILURE) {
		efree(argv);
		return;
	}

	if (vips_php_call_array(operation_name, instance, 
		"", argc - 2, argv + 2, return_value)) {
		efree(argv);
		return;
	}

	efree(argv);

}
/* }}} */

/* {{{ proto resource vips_image_new_from_file(string filename [, array options])
   Open an image from a filename */
PHP_FUNCTION(vips_image_new_from_file)
{
	char *name;
	size_t name_len;
	zval *options;
	char filename[VIPS_PATH_MAX];
	char option_string[VIPS_PATH_MAX];
	const char *operation_name;
	zval argv[2];
	int argc;

	VIPS_DEBUG_MSG("vips_image_new_from_file:\n");

	options = NULL;
	if (zend_parse_parameters(ZEND_NUM_ARGS(), "p|a", 
		&name, &name_len, &options) == FAILURE) {
		return;
	}
	VIPS_DEBUG_MSG("vips_image_new_from_file: name = %s\n", name);

	vips__filename_split8(name, filename, option_string);
	if (!(operation_name = vips_foreign_find_load(filename))) {
		error_vips();
		return;
	}

	argc = 1;
	ZVAL_STRING(&argv[0], filename);
	if (options) {
		ZVAL_ARR(&argv[1], Z_ARR_P(options));
		argc += 1;
	}

	if (vips_php_call_array(operation_name, NULL, 
		option_string, argc, argv, return_value)) {
		error_vips();
		return;
	}

	zval_dtor(&argv[0]);
}
/* }}} */

/* {{{ proto resource vips_image_new_from_buffer(string buffer [, string option_string, array options])
   Open an image from a string */
PHP_FUNCTION(vips_image_new_from_buffer)
{
	char *buffer;
	size_t buffer_len;
	char *option_string;
	size_t option_string_len;
	zval *options;
	const char *operation_name;
	zval argv[2];
	int argc;

	VIPS_DEBUG_MSG("vips_image_new_from_buffer:\n");

	option_string = NULL;
	options = NULL;
	if (zend_parse_parameters(ZEND_NUM_ARGS(), "s|sa", 
		&buffer, &buffer_len, &option_string, &option_string_len, 
		&options) == FAILURE) {
		return;
	}

	if (!(operation_name = vips_foreign_find_load_buffer(buffer, buffer_len))) {
		error_vips();
		return;
	}

	argc = 1;
	ZVAL_STRINGL(&argv[0], buffer, buffer_len);
	if (options) {
		ZVAL_ARR(&argv[1], Z_ARR_P(options));
		argc += 1;
	}

	if (vips_php_call_array(operation_name, NULL, 
		option_string, argc, argv, return_value)) {
		error_vips();
		return;
	}

	zval_dtor(&argv[0]);
}
/* }}} */

/* {{{ proto resource vips_image_new_from_array(array coefficients [, double scale, double offset])
   Open an image from a string */
PHP_FUNCTION(vips_image_new_from_array)
{
	zval *array;
	double scale;
	double offset;
	int width;
	int height;
	VipsImage *mat;
	int x, y;
	zval *row;

	VIPS_DEBUG_MSG("vips_image_new_from_array:\n");

	scale = 1.0;
	offset = 0.0;
	if (zend_parse_parameters(ZEND_NUM_ARGS(), "a|dd", 
		&array, &scale, &offset) == FAILURE) {
		return;
	}

	height = zend_hash_num_elements(Z_ARRVAL_P(array));
	if ((row = zend_hash_index_find(Z_ARRVAL_P(array), 0)) == NULL) {
		php_error_docref(NULL, E_WARNING, "no element zero");
		return;
	}
	if (is_2D(array)) {
		mat =  matrix_from_zval(array);
	}
	else {
		/* 1D array.
		 */
		width = height;
		height = 1;

		mat = vips_image_new_matrix(width, height);

		for (x = 0; x < width; x++) {
			zval *ele;

			ele = zend_hash_index_find(Z_ARRVAL_P(array), x);
			convert_to_double_ex(ele);
			*VIPS_MATRIX(mat, x, 0) = zval_get_double(ele);
		}
	}

	vips_image_set_double(mat, "scale", scale);
	vips_image_set_double(mat, "offset", offset);

	RETURN_RES(zend_register_resource(mat, le_gobject));
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

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "rp|a", 
		&IM, &filename, &filename_len, &options) == FAILURE) {
		RETURN_FALSE;
	}

	if ((image = (VipsImage *)zend_fetch_resource(Z_RES_P(IM), 
		"GObject", le_gobject)) == NULL) {
		RETURN_FALSE;
	}

	if (vips_image_write_to_file(image, filename, NULL)) {
		error_vips();
		RETURN_FALSE;
	}

	RETURN_TRUE;
}
/* }}} */

/* {{{ proto string vips_image_write_to_buffer(resource image, string suffix [, array options])
   Write an image to a string */
PHP_FUNCTION(vips_image_write_to_buffer)
{
	zval *IM;
	zval *options = NULL;
	char *suffix;
	size_t suffix_len;
	VipsImage *image;
	char filename[VIPS_PATH_MAX];
	char option_string[VIPS_PATH_MAX];
	const char *operation_name;
	zval argv[1];
	int argc;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "rs|a", 
		&IM, &suffix, &suffix_len, &options) == FAILURE) {
		RETURN_FALSE;
	}

	if ((image = (VipsImage *)zend_fetch_resource(Z_RES_P(IM), 
		"GObject", le_gobject)) == NULL) {
		RETURN_FALSE;
	}

	vips__filename_split8(suffix, filename, option_string);
	if (!(operation_name = vips_foreign_find_save_buffer(filename))) {
		error_vips();
		return;
	}

	argc = 0;
	if (options) {
		ZVAL_ARR(&argv[0], Z_ARR_P(options));
		argc += 1;
	}

	if (vips_php_call_array(operation_name, IM, 
		option_string, argc, argv, return_value)) {
		error_vips();
		return;
	}
}
/* }}} */

/* {{{ proto value vips_image_get(resource image, string field)
   Fetch field from image */
PHP_FUNCTION(vips_image_get)
{
	zval *im;
	char *field_name;
	size_t field_name_len;
	VipsImage *image;
	GValue gvalue = { 0 };

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "rs", 
		&im, &field_name, &field_name_len) == FAILURE) {
		return;
	}

	if ((image = (VipsImage *)zend_fetch_resource(Z_RES_P(im), 
		"GObject", le_gobject)) == NULL) {
		return;
	}

	if (vips_image_get(image, field_name, &gvalue)) {
		error_vips();
		return;
	}

	if (vips_php_gval_to_zval(&gvalue, return_value)) {
		g_value_unset(&gvalue);
		return;
	}
	g_value_unset(&gvalue);
}
/* }}} */

/* {{{ proto long vips_image_get_typeof(resource image, string field)
   Fetch type of field from image */
PHP_FUNCTION(vips_image_get_typeof)
{
	zval *im;
	char *field_name;
	size_t field_name_len;
	VipsImage *image;
	GType type;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "rs", 
		&im, &field_name, &field_name_len) == FAILURE) {
		return;
	}

	if ((image = (VipsImage *)zend_fetch_resource(Z_RES_P(im), 
		"GObject", le_gobject)) == NULL) {
		return;
	}

	type = vips_image_get_typeof(image, field_name); 
	
	RETURN_LONG(type);
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
static void php_free_gobject(zend_resource *rsrc)
{
	VIPS_DEBUG_MSG("php_free_gobject: %p\n", rsrc->ptr);

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
	if (VIPS_INIT("banana"))
		return FAILURE;

	le_gobject = zend_register_list_destructors_ex(php_free_gobject, 
		NULL, "GObject", module_number);

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

ZEND_BEGIN_ARG_INFO(arginfo_vips_image_new_from_buffer, 0)
	ZEND_ARG_INFO(0, buffer)
	ZEND_ARG_INFO(0, option_string)
	ZEND_ARG_INFO(0, options)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_vips_image_new_from_array, 0)
	ZEND_ARG_INFO(0, array)
	ZEND_ARG_INFO(0, scale)
	ZEND_ARG_INFO(0, offset)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_vips_image_write_to_file, 0)
	ZEND_ARG_INFO(0, image)
	ZEND_ARG_INFO(0, filename)
	ZEND_ARG_INFO(0, options)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_vips_image_write_to_buffer, 0)
	ZEND_ARG_INFO(0, image)
	ZEND_ARG_INFO(0, options)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_vips_call, 0)
	ZEND_ARG_INFO(0, operation_name)
	ZEND_ARG_INFO(0, instance)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_vips_image_get, 0)
	ZEND_ARG_INFO(0, image)
	ZEND_ARG_INFO(0, field)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO(arginfo_vips_image_get_typeof, 0)
	ZEND_ARG_INFO(0, image)
	ZEND_ARG_INFO(0, field)
ZEND_END_ARG_INFO()

/* {{{ vips_functions[]
 *
 * Every user visible function must have an entry in vips_functions[].
 */
const zend_function_entry vips_functions[] = {
	PHP_FE(vips_image_new_from_file, arginfo_vips_image_new_from_file)
	PHP_FE(vips_image_new_from_buffer, arginfo_vips_image_new_from_buffer)
	PHP_FE(vips_image_new_from_array, arginfo_vips_image_new_from_array)
	PHP_FE(vips_image_write_to_file, arginfo_vips_image_write_to_file)
	PHP_FE(vips_image_write_to_buffer, arginfo_vips_image_write_to_buffer)
	PHP_FE(vips_call, arginfo_vips_call)
	PHP_FE(vips_image_get, arginfo_vips_image_get)
	PHP_FE(vips_image_get_typeof, arginfo_vips_image_get_typeof)

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
