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
 * @link      https://github.com/jcupitt/php-vips
 */

namespace Jcupitt\Vips;

/**
 * This class contains the libvips FFI methods.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/jcupitt/php-vips
 */
class FFI
{

    /**
     * The FFI handle we use for the glib binary.
     *
     * @internal
     */
    private static \FFI $glib;

    /**
     * The FFI handle we use for the gobject binary.
     *
     * @internal
     */
    private static \FFI $gobject;

    /**
     * The FFI handle we use for the libvips binary.
     *
     * @internal
     */
    private static \FFI $vips;

    /**
     * Are the above FFI handles initialized?
     *
     * @internal
     */
    private static bool $ffi_inited = false;

    /**
     * Look up these once.
     *
     * @internal
     */
    private static array $ctypes;
    private static array $gtypes;
    private static array $ftypes;

    /**
     * The library version number we detect.
     *
     * @internal
     */
    private static int $library_major;
    private static int $library_minor;
    private static int $library_micro;

    public static function glib(): \FFI
    {
        self::init();

        return self::$glib;
    }

    public static function gobject(): \FFI
    {
        self::init();

        return self::$gobject;
    }

    public static function vips(): \FFI
    {
        self::init();

        return self::$vips;
    }

    public static function ctypes(string $name): \FFI\CType
    {
        self::init();

        return self::$ctypes[$name];
    }

    public static function gtypes(string $name): int
    {
        self::init();

        return self::$gtypes[$name];
    }

    public static function ftypes(string $name): string
    {
        self::init();

        return self::$ftypes[$name];
    }

    /**
     * Gets the libvips version number as a string of the form
     * MAJOR.MINOR.MICRO, for example "8.6.1".
     *
     * @return string
     */
    public static function version(): string
    {
        self::init();

        return self::$library_major . "." .
            self::$library_minor . "." .
            self::$library_micro;
    }

    /**
     * Is this at least libvips major.minor[.patch]?
     * @param int $x Major component.
     * @param int $y Minor component.
     * @param int $z Patch component.
     * @return bool `true` if at least libvips major.minor[.patch]; otherwise, `false`.
     */
    public static function atLeast(int $x, int $y, int $z = 0): bool
    {
        return self::$library_major > $x ||
            self::$library_major == $x && self::$library_minor > $y ||
            self::$library_major == $x && self::$library_minor == $y && self::$library_micro >= $z;
    }

    /**
     * Shut down libvips. Call this just before process exit.
     *
     * @return void
     */
    public static function shutDown(): void
    {
        self::vips()->vips_shutdown();
    }

    private static function libraryName(string $name, int $abi): string
    {
        switch (PHP_OS_FAMILY) {
            case "Windows":
                return "$name-$abi.dll";

            case "OSX":
            case "Darwin":
                return "$name.$abi.dylib";

            default:
                // most *nix
                return "$name.so.$abi";
        }
    }

    private static function init(): void
    {
        // Already initialized.
        if (self::$ffi_inited) {
            return;
        }

        // try experimentally binding a bit of stdio ... if this fails, FFI
        // has probably not been installed or enabled, and php will throw a
        // useful error message
        $stdio = \FFI::cdef(<<<EOS
            int printf(const char *, ...);
        EOS);

        $vips_libname = self::libraryName("libvips", 42);
        if (PHP_OS_FAMILY === "Windows") {
            $glib_libname = self::libraryName("libglib-2.0", 0);
            $gobject_libname = self::libraryName("libgobject-2.0", 0);
        } else {
            $glib_libname = $vips_libname;
            $gobject_libname = $vips_libname;
        }

        Utils::debugLog("init", ["library" => $vips_libname]);

        $is_64bits = PHP_INT_SIZE === 8;

        $libraryPaths = [
            "" // system library
        ];

        $vipshome = getenv("VIPSHOME");
        if ($vipshome) {
            // lib<qual>/ predicates lib/
            $libraryPaths[] = $vipshome . ($is_64bits ? "/lib64/" : "/lib32/");
            // lib/ is always searched
            $libraryPaths[] = $vipshome . "/lib/";
        }

        if (PHP_OS_FAMILY === "OSX" ||
            PHP_OS_FAMILY === "Darwin") {
            $libraryPaths[] = "/opt/homebrew/lib/"; // Homebrew on Apple Silicon
        }

        // attempt to open libraries using the system library search method
        // (no prefix) and a couple of fallback paths, if any
        $vips = null;
        foreach ($libraryPaths as $path) {
            Utils::debugLog("init", ["path" => $path]);

            try {
                $vips = \FFI::cdef(<<<EOS
                    int vips_init (const char *argv0);
                    const char *vips_error_buffer (void);
                    int vips_version(int flag);
                EOS, $path . $vips_libname);
                break;
            } catch (\FFI\Exception $e) {
                Utils::debugLog("init", ["msg" => "library load failed", "exception" => $e]);
            }
        }

        if ($vips === null) {
            array_shift($libraryPaths);

            $msg = "Unable to open library '$vips_libname'";
            if (!empty($libraryPaths)) {
                $msg .= " in any of ['" . implode("', '", $libraryPaths) . "']";
            }
            $msg .= ". Make sure that you've installed libvips and that '$vips_libname'";
            $msg .= " is on your system's library search path.";
            throw new Exception($msg);
        }

        $result = $vips->vips_init("");
        if ($result != 0) {
            throw new Exception("libvips error: " . $vips->vips_error_buffer());
        }
        Utils::debugLog("init", ["vips_init" => $result]);

        # get the library version number, then we can build the API
        self::$library_major = $vips->vips_version(0);
        self::$library_minor = $vips->vips_version(1);
        self::$library_micro = $vips->vips_version(2);
        Utils::debugLog("init", [
            "libvips version" => [
                self::$library_major,
                self::$library_minor,
                self::$library_micro
            ]
        ]);

        if (!self::atLeast(8, 7)) {
            throw new Exception("your libvips is too old -- " .
                "8.7 or later required");
        }

        // GType is the size of a pointer
        $gtype = $is_64bits ? "guint64" : "guint32";

        // Typedefs shared across the libvips, GLib and GObject declarations
        $typedefs = <<<EOS
// we need the glib names for these types
typedef uint32_t guint32;
typedef int32_t gint32;
typedef uint64_t guint64;
typedef int64_t gint64;

typedef $gtype GType;

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
EOS;

        // GLib declarations
        $glib_decls = $typedefs . <<<EOS
void* g_malloc (size_t size);
void g_free (void* data);
EOS;

        // GObject declarations
        $gobject_decls = $typedefs . <<<EOS
typedef struct _GValue {
    GType g_type;
    guint64 data[2];
} GValue;

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

const char* g_type_name (GType gtype);
GType g_type_from_name (const char* name);

void g_value_init (GValue* value, GType gtype);
void g_value_unset (GValue* value);
GType g_type_fundamental (GType gtype);

void g_value_set_boolean (GValue* value, bool v_boolean);
void g_value_set_int (GValue* value, int i);
void g_value_set_uint64 (GValue* value, guint64 ull);
void g_value_set_int64 (GValue* value, guint64 ull);
void g_value_set_double (GValue* value, double d);
void g_value_set_enum (GValue* value, int e);
void g_value_set_flags (GValue* value, unsigned int f);
void g_value_set_string (GValue* value, const char* str);
void g_value_set_object (GValue* value, void* object);

bool g_value_get_boolean (const GValue* value);
int g_value_get_int (GValue* value);
guint64 g_value_get_uint64 (GValue* value);
gint64 g_value_get_int64 (GValue* value);
double g_value_get_double (GValue* value);
int g_value_get_enum (GValue* value);
unsigned int g_value_get_flags (GValue* value);
const char* g_value_get_string (GValue* value);
void* g_value_get_object (GValue* value);

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

const char* g_param_spec_get_blurb (GParamSpec* psp);
EOS;

        # the whole libvips API, mostly adapted from pyvips
        $vips_decls = $typedefs . <<<EOS
typedef struct _VipsImage VipsImage;
typedef struct _VipsProgress VipsProgress;

// Defined in GObject, just typedef to void
typedef void GParamSpec;
typedef void GValue;

int vips_init (const char *argv0);
int vips_shutdown (void);

const char *vips_error_buffer (void);
void vips_error_clear (void);
void vips_error_freeze (void);
void vips_error_thaw (void);

int vips_version(int flag);

void vips_leak_set (int leak);

GType vips_type_find (const char* basename, const char* nickname);
const char* vips_nickname_find (GType type);

typedef void* (*VipsTypeMap2Fn) (GType type, void* a, void* b);
void* vips_type_map (GType base, VipsTypeMap2Fn fn, void* a, void* b);

int vips_enum_from_nick (const char* domain,
    GType gtype, const char* str);
const char *vips_enum_nick (GType gtype, int value);

void vips_value_set_ref_string (GValue* value, const char* str);
void vips_value_set_array_double (GValue* value,
const double* array, int n );
void vips_value_set_array_int (GValue* value,
const int* array, int n );
void vips_value_set_array_image (GValue *value, int n);
typedef void (*FreeFn)(void* a);
void vips_value_set_blob (GValue* value,
    FreeFn free_fn, void* data, size_t length);

const char* vips_value_get_ref_string (const GValue* value,
    size_t* length);
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

int vips_object_get_argument (VipsObject* object, const char *name, 
    GParamSpec** pspec,
    VipsArgumentClass** argument_class,
    VipsArgumentInstance** argument_instance);

void vips_object_print_all (void);

int vips_object_set_from_string (VipsObject* object,
    const char* options);

const char* vips_object_get_description (VipsObject* object);

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
VipsImage* vips_image_new_from_memory_copy (const void *data, size_t size,
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

void vips_concurrency_set( int concurrency );

void vips_cache_set_max (int max);
void vips_cache_set_max_mem (size_t max_mem);
void vips_cache_set_max_files (int max_files);
void vips_cache_set_trace (int trace);

int vips_cache_get_max();
int vips_cache_get_size();
size_t vips_cache_get_max_mem();
int vips_cache_get_max_files();

size_t vips_tracked_get_mem_highwater();
size_t vips_tracked_get_mem();
int vips_tracked_get_allocs();
int vips_tracked_get_files();

char** vips_image_get_fields (VipsImage* image);
int vips_image_hasalpha (VipsImage* image);

GType vips_blend_mode_get_type (void);
void vips_value_set_blob_free (GValue* value, void* data, size_t length);

int vips_object_get_args (VipsObject* object,
    const char*** names, int** flags, int* n_args);
EOS;

        if (self::atLeast(8, 8)) {
            $vips_decls = $vips_decls . <<<EOS
char** vips_foreign_get_suffixes (void);

void* vips_region_fetch (VipsRegion*, int, int, int, int,
    size_t* length);
int vips_region_width (VipsRegion*);
int vips_region_height (VipsRegion*);
int vips_image_get_page_height (VipsImage*);
int vips_image_get_n_pages (VipsImage*);
EOS;
        }

        if (self::atLeast(8, 8)) {
            $vips_decls = $vips_decls . <<<EOS
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

        Utils::debugLog("init", ["binding ..."]);
        self::$glib = \FFI::cdef($glib_decls, $glib_libname);
        self::$gobject = \FFI::cdef($gobject_decls, $gobject_libname);
        self::$vips = \FFI::cdef($vips_decls, $vips_libname);

        # Useful for debugging
        # self::$vips->vips_leak_set(1);

        # force the creation of some types we need
        self::$vips->vips_blend_mode_get_type();
        self::$vips->vips_interpretation_get_type();
        self::$vips->vips_operation_flags_get_type();
        self::$vips->vips_band_format_get_type();
        self::$vips->vips_token_get_type();
        self::$vips->vips_saveable_get_type();
        self::$vips->vips_image_type_get_type();

        // look these up in advance
        self::$ctypes = [
            "GObject" => self::$gobject->type("GObject*"),
            "GParamSpec" => self::$gobject->type("GParamSpec*"),
            "VipsObject" => self::$vips->type("VipsObject*"),
            "VipsOperation" => self::$vips->type("VipsOperation*"),
            "VipsImage" => self::$vips->type("VipsImage*"),
            "VipsInterpolate" => self::$vips->type("VipsInterpolate*"),
        ];

        self::$gtypes = [
            "gboolean" => self::$gobject->g_type_from_name("gboolean"),
            "gint" => self::$gobject->g_type_from_name("gint"),
            "gint64" => self::$gobject->g_type_from_name("gint64"),
            "guint64" => self::$gobject->g_type_from_name("guint64"),
            "gdouble" => self::$gobject->g_type_from_name("gdouble"),
            "gchararray" => self::$gobject->g_type_from_name("gchararray"),
            "VipsRefString" => self::$gobject->g_type_from_name("VipsRefString"),

            "GEnum" => self::$gobject->g_type_from_name("GEnum"),
            "GFlags" => self::$gobject->g_type_from_name("GFlags"),
            "VipsBandFormat" => self::$gobject->g_type_from_name("VipsBandFormat"),
            "VipsBlendMode" => self::$gobject->g_type_from_name("VipsBlendMode"),
            "VipsArrayInt" => self::$gobject->g_type_from_name("VipsArrayInt"),
            "VipsArrayDouble" =>
                self::$gobject->g_type_from_name("VipsArrayDouble"),
            "VipsArrayImage" => self::$gobject->g_type_from_name("VipsArrayImage"),
            "VipsBlob" => self::$gobject->g_type_from_name("VipsBlob"),

            "GObject" => self::$gobject->g_type_from_name("GObject"),
            "VipsImage" => self::$gobject->g_type_from_name("VipsImage"),
        ];

        // map vips format names to c type names
        self::$ftypes = [
            "char" => "char",
            "uchar" => "unsigned char",
            "short" => "short",
            "ushort" => "unsigned short",
            "int" => "int",
            "uint" => "unsigned int",
            "float" => "float",
            "double" => "double",
            "complex" => "float",
            "dpcomplex" => "double",
        ];

        Utils::debugLog("init", ["done"]);
        self::$ffi_inited = true;
    }
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
