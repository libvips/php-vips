<?php

/**
 * Vips is a php binding for the vips image processing library
 *
 * PHP version 7
 *
 * LICENSE:
 *
 * Copyright (c) 2016 John Cupitt
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/libvips/php-vips
 */

namespace Jcupitt\Vips;

/* This file does all the init we need to start up libvips and the binding.
 */

echo "*** Init.php: startup\n";

$library_name = "libvips";

# PHP_OS_FAMILY added in php 7.2
switch(PHP_OS_FAMILY) {
case "Windows":
    $library_ext = ".dll";
    break;

case "OSX":
    $library_ext = ".dylib";
    break;

default;
    $library_ext = ".so";
    break;
}

$vipshome = getenv("VIPSHOME");
if ($vipshome) {
    $library_location = $vipshome . "/lib/";
}
else {
    # rely on ffi's search
    $library_location = "";
}

$library = "$library_location$library_name$library_ext";

echo "*** Init.php: library = $library\n";

# FFI added in 7.4
$base_ffi = FFI::cdef(<<<EOS
int vips_init (const char *argv0);
int vips_shutdown (void);

const char *vips_error_buffer (void);
char *vips_error_buffer_copy (void);
void vips_error_clear (void);
void vips_error_freeze (void);
void vips_error_thaw (void);

int vips_version(int flag);
EOS, $library);

function error($message = "")
{
    if (strlen($message) == 0) {
        global $base_ffi;

        $message = "libvips error: $base_ffi->vips_error_buffer()";
        $base_ffi->vips_error_clear();
    }

    throw new Exception($message);
}

$result = $base_ffi->vips_init($argv[0]);
if ($result != 0) {
    error();
}
Utils::debugLog("vips_init: $result");

# get the library version number, then we can build the API
$library_major = $base_ffi->vips_version(0);
$library_minor = $base_ffi->vips_version(1);
$library_micro = $base_ffi->vips_version(2);
Utils::debugLog("found libvips version: $library_major.$library_minor.$library_micro");

function at_least($need_major, $need_minor)
{
    global $library_major, $library_minor;

    return $need_major < $library_major || 
        ($need_major == $library_major && $need_minor <= $library_minor);
}

if (!at_least(8, 7)) {
    error("your libvips is too old -- 8.7 or later required");
}

if (PHP_INT_SIZE != 8) {
    # we could maybe fix this if it's important ... it's mostly necessary since
    # GType is the size of a pointer, and there's no easy way to discover if php
    # is running on a 32 or 64-bit systems (as far as I can see)
    error("your php only supports 32-bit ints -- 64 bit ints required");
}

// bind the libvips API to the library binary

# largely copied from pyvips
$header = <<<EOS
// we need the glib names for these types
typedef uint32_t guint32;
typedef int32_t gint32;
typedef uint64_t guint64;
typedef int64_t gint64;

// FIXME ... need to detect 32/64 bit platform, since GType is an int 
// the size of a pointer, see PHP_INT_SIZE above
typedef guint64 GType;

typedef struct _VipsImage VipsImage;
typedef struct _VipsProgress VipsProgress;
typedef struct _GValue GValue;

void* g_malloc (size_t size);
void g_free (void* data);

void vips_leak_set (int leak);

char* vips_path_filename7 (const char* path);
char* vips_path_mode7 (const char* path);

GType vips_type_find (const char* basename, const char* nickname);
const char* vips_nickname_find (GType type);

const char* g_type_name (GType gtype);
GType g_type_from_name (const char* name);

typedef void* (*VipsTypeMap2Fn) (GType type, void* a, void* b);
void* vips_type_map (GType base, VipsTypeMap2Fn fn, void* a, void* b);

typedef struct _GValue {
GType g_type;
guint64 data[2];
} GValue;

void g_value_init (GValue* value, GType gtype);
void g_value_unset (GValue* value);
GType g_type_fundamental (GType gtype);

int vips_enum_from_nick (const char* domain,
GType gtype, const char* str);
const char *vips_enum_nick (GType gtype, int value);

void g_value_set_boolean (GValue* value, bool v_boolean);
void g_value_set_int (GValue* value, int i);
void g_value_set_uint64 (GValue* value, guint64 ull);
void g_value_set_double (GValue* value, double d);
void g_value_set_enum (GValue* value, int e);
void g_value_set_flags (GValue* value, unsigned int f);
void g_value_set_string (GValue* value, const char* str);
void vips_value_set_ref_string (GValue* value, const char* str);
void g_value_set_object (GValue* value, void* object);
void vips_value_set_array_double (GValue* value,
const double* array, int n );
void vips_value_set_array_int (GValue* value,
const int* array, int n );
void vips_value_set_array_image (GValue *value, int n);
typedef void (*FreeFn)(void* a);
void vips_value_set_blob (GValue* value,
FreeFn free_fn, void* data, size_t length);

bool g_value_get_boolean (const GValue* value);
int g_value_get_int (GValue* value);
guint64 g_value_get_uint64 (GValue* value);
double g_value_get_double (GValue* value);
int g_value_get_enum (GValue* value);
unsigned int g_value_get_flags (GValue* value);
const char* g_value_get_string (GValue* value);
const char* vips_value_get_ref_string (const GValue* value,
size_t* length);
void* g_value_get_object (GValue* value);
double* vips_value_get_array_double (const GValue* value, int* n);
int* vips_value_get_array_int (const GValue* value, int* n);
VipsImage** vips_value_get_array_image (const GValue* value, int* n);
void* vips_value_get_blob (const GValue* value, size_t* length);

// need to make some of these by hand
GType vips_interpretation_get_type (void);
GType vips_operation_flags_get_type (void);
GType vips_band_format_get_type (void);
GType vips_token_get_type (void);
GType vips_saveable_get_type (void);
GType vips_image_type_get_type (void);

typedef struct _GData GData;

typedef struct _GTypeClass GTypeClass;

typedef struct _GTypeInstance {
GTypeClass *g_class;
} GTypeInstance;

typedef struct _GObject {
GTypeInstance g_type_instance;
unsigned int ref_count;
GData *qdata;
} GObject;

typedef struct _GParamSpec {
GTypeInstance g_type_instance;

const char* name;
unsigned int flags;
GType value_type;
GType owner_type;

// private, but cffi in API mode needs these to be able to get the
// offset of any member
char* _nick;
char* _blurb;
GData* qdata;
unsigned int ref_count;
unsigned int param_id;
} GParamSpec;

typedef struct _GEnumValue {
int value;

const char *value_name;
const char *value_nick;
} GEnumValue;

typedef struct _GEnumClass {
GTypeClass *g_type_class;

int minimum;
int maximum;
unsigned int n_values;
GEnumValue *values;
} GEnumClass;

typedef struct _GFlagsValue {
unsigned int value;

const char *value_name;
const char *value_nick;
} GFlagsValue;

typedef struct _GFlagsClass {
GTypeClass *g_type_class;

unsigned int mask;
unsigned int n_values;
GFlagsValue *values;
} GFlagsClass;

void* g_type_class_ref (GType type);

void* g_object_new (GType type, void*);
void g_object_ref (void* object);
void g_object_unref (void* object);

void g_object_set_property (GObject* object,
const char *name, GValue* value);
void g_object_get_property (GObject* object,
const char* name, GValue* value);

typedef void (*GCallback)(void);
typedef void (*GClosureNotify)(void* data, struct _GClosure *);
long g_signal_connect_data (GObject* object,
const char* detailed_signal,
GCallback c_handler,
void* data,
GClosureNotify destroy_data,
int connect_flags);

void vips_image_set_progress (VipsImage* image, bool progress);
void vips_image_set_kill (VipsImage* image, bool kill);

typedef struct _VipsProgress {
VipsImage* im;

int run;
int eta;
gint64 tpels;
gint64 npels;
int percent;
void* start;
} VipsProgress;

typedef struct _VipsObject {
GObject parent_instance;

bool constructed;
bool static_object;
void *argument_table;
char *nickname;
char *description;
bool preclose;
bool close;
bool postclose;
size_t local_memory;
} VipsObject;

typedef struct _VipsObjectClass VipsObjectClass;

typedef struct _VipsArgument {
GParamSpec *pspec;
} VipsArgument;

typedef struct _VipsArgumentInstance {
VipsArgument parent;

// more
} VipsArgumentInstance;

typedef enum _VipsArgumentFlags {
VIPS_ARGUMENT_NONE = 0,
VIPS_ARGUMENT_REQUIRED = 1,
VIPS_ARGUMENT_CONSTRUCT = 2,
VIPS_ARGUMENT_SET_ONCE = 4,
VIPS_ARGUMENT_SET_ALWAYS = 8,
VIPS_ARGUMENT_INPUT = 16,
VIPS_ARGUMENT_OUTPUT = 32,
VIPS_ARGUMENT_DEPRECATED = 64,
VIPS_ARGUMENT_MODIFY = 128
} VipsArgumentFlags;

typedef struct _VipsArgumentClass {
VipsArgument parent;

VipsObjectClass *object_class;
VipsArgumentFlags flags;
int priority;
unsigned int offset;
} VipsArgumentClass;

int vips_object_get_argument (VipsObject* object,
const char *name, GParamSpec** pspec,
VipsArgumentClass** argument_class,
VipsArgumentInstance** argument_instance);

void vips_object_print_all (void);

int vips_object_set_from_string (VipsObject* object,
const char* options);

const char* vips_object_get_description (VipsObject* object);

const char* g_param_spec_get_blurb (GParamSpec* psp);

typedef struct _VipsImage {
VipsObject parent_instance;
// more
} VipsImage;
const char* vips_foreign_find_load (const char* name);
const char* vips_foreign_find_load_buffer (const void* data,
size_t size);
const char* vips_foreign_find_save (const char* name);
const char* vips_foreign_find_save_buffer (const char* suffix);

VipsImage* vips_image_new_matrix_from_array (int width, int height,
const double* array, int size);
VipsImage* vips_image_new_from_memory (const void* data, size_t size,
int width, int height, int bands, int format);

VipsImage* vips_image_copy_memory (VipsImage* image);

GType vips_image_get_typeof (const VipsImage* image,
const char* name);
int vips_image_get (const VipsImage* image,
const char* name, GValue* value_copy);
void vips_image_set (VipsImage* image,
const char* name, GValue* value);
int vips_image_remove (VipsImage* image, const char* name);

char* vips_filename_get_filename (const char* vips_filename);
char* vips_filename_get_options (const char* vips_filename);

VipsImage* vips_image_new_temp_file (const char* format);

int vips_image_write (VipsImage* image, VipsImage* out);
void* vips_image_write_to_memory (VipsImage* in, size_t* size_out);

typedef struct _VipsInterpolate {
VipsObject parent_object;

// more
} VipsInterpolate;

VipsInterpolate* vips_interpolate_new (const char* name);

typedef struct _VipsOperation {
VipsObject parent_instance;

// more
} VipsOperation;

VipsOperation* vips_operation_new (const char* name);

typedef void* (*VipsArgumentMapFn) (VipsObject* object,
GParamSpec* pspec,
VipsArgumentClass* argument_class,
VipsArgumentInstance* argument_instance,
void* a, void* b);

void* vips_argument_map (VipsObject* object,
VipsArgumentMapFn fn, void* a, void* b);

typedef struct _VipsRegion {
VipsObject parent_object;

// more
} VipsRegion;
VipsRegion* vips_region_new (VipsImage*);

VipsOperation* vips_cache_operation_build (VipsOperation* operation);
void vips_object_unref_outputs (VipsObject* object);

int vips_operation_get_flags (VipsOperation* operation);

void vips_cache_set_max (int max);
void vips_cache_set_max_mem (size_t max_mem);
void vips_cache_set_max_files (int max_files);
void vips_cache_set_trace (int trace);

int vips_cache_get_max();
int vips_cache_get_size();
size_t vips_cache_get_max_mem();
int vips_cache_get_max_files();

char** vips_image_get_fields (VipsImage* image);
int vips_image_hasalpha (VipsImage* image);

GType vips_blend_mode_get_type (void);
void vips_value_set_blob_free (GValue* value, void* data, size_t length);

int vips_object_get_args (VipsObject* object,
const char*** names, int** flags, int* n_args);
EOS;

if (at_least(8, 8)) {
    $header = $header . <<<EOS
char** vips_foreign_get_suffixes (void);

void* vips_region_fetch (VipsRegion*, int, int, int, int,
size_t* length);
int vips_region_width (VipsRegion*);
int vips_region_height (VipsRegion*);
int vips_image_get_page_height (VipsImage*);
int vips_image_get_n_pages (VipsImage*);
EOS;
}

if (at_least(8, 8)) {
    $header = $header . <<<EOS
typedef struct _VipsConnection {
VipsObject parent_object;

// more
} VipsConnection;

const char* vips_connection_filename (VipsConnection* stream);
const char* vips_connection_nick (VipsConnection* stream);

typedef struct _VipsSource {
VipsConnection parent_object;

// more
} VipsSource;

VipsSource* vips_source_new_from_descriptor (int descriptor);
VipsSource* vips_source_new_from_file (const char* filename);
VipsSource* vips_source_new_from_memory (const void* data,
size_t size);

typedef struct _VipsSourceCustom {
VipsSource parent_object;

// more
} VipsSourceCustom;

VipsSourceCustom* vips_source_custom_new (void);

// FIXME ... these need porting to php-ffi
// extern "Python" gint64 _marshal_read (VipsSource*,
//    void*, gint64, void*);
// extern "Python" gint64 _marshal_seek (VipsSource*,
//    gint64, int, void*);

typedef struct _VipsTarget {
VipsConnection parent_object;

// more
} VipsTarget;

VipsTarget* vips_target_new_to_descriptor (int descriptor);
VipsTarget* vips_target_new_to_file (const char* filename);
VipsTarget* vips_target_new_to_memory (void);

typedef struct _VipsTargetCustom {
VipsTarget parent_object;

// more
} VipsTargetCustom;

VipsTargetCustom* vips_target_custom_new (void);

const char* vips_foreign_find_load_source (VipsSource *source);
const char* vips_foreign_find_save_target (const char* suffix);
EOS;
}

echo "*** Init.php: building binding ...\n";

$ffi = FFI::cdef($header, $library);

# FIXME make ctypes and gtypes

/*
* Local variables:
* tab-width: 4
* c-basic-offset: 4
* End:
* vim600: expandtab sw=4 ts=4 fdm=marker
* vim<600: expandtab sw=4 ts=4
*/

