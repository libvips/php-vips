#!/usr/bin/env php
<?php

$trace = TRUE;

function trace($message)
{
  global $trace;

  if ($trace) {
    echo "$message\n";
  }
}

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
  # reply on ffi's search
  $library_location = "";
}

$library = "$library_location$library_name$library_ext";

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

function error()
{
  global $base_ffi;

  throw new Exception("libvips error: $base_ffi->vips_error_buffer()");
}

$result = $base_ffi->vips_init($argv[0]);
if ($result != 0) {
      error();
}
trace("vips_init: $result");

# get the library version number, then we can build the API
$library_major = $base_ffi->vips_version(0);
$library_minor = $base_ffi->vips_version(1);
$library_micro = $base_ffi->vips_version(2);
trace("found libvips version: $library_major.$library_minor.$library_micro");

function at_least($need_major, $need_minor)
{
  global $library_major, $library_minor;

  return $need_major < $library_major || 
    ($need_major == $library_major && $need_minor <= $library_minor);
}

if (!at_least(8, 7)) {
  trace("your libvips is too old -- 8.7 or later required");
  exit(1);
}

if (PHP_INT_SIZE != 8) {
  # we could maybe fix this if it's important ... it's mostly necessary since
  # GType is the size of a pointer, and there's no easy way to discover if php
  # is running on a 32 or 64-bit systems (as far as I can see)
  trace("your php only supports 32-bit ints -- 64 bit ints required");
  exit(1);
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

$ffi = FFI::cdef($header, $library);

// open an image file

if (count($argv) < 2) {
  trace("usage: ./vips-ffi.php <image-file-name>");
  exit(1);
}
$filename = $argv[1];

trace("attempting to open: $filename");

$loader = $ffi->vips_foreign_find_load($filename);
trace("selected loader: $loader");

$operation = $ffi->vips_operation_new($loader);
if (FFI::isNull($operation)) {
  error();
}

// now introspect $operation and show all the args it wants ... about the next
// 250 lines of code

$vipsObject = $ffi->type("VipsObject*");
$gObject = $ffi->type("GObject*");
$description = $ffi->vips_object_get_description(
  FFI::cast($vipsObject, $operation));
trace("description: $description");

$operationFlags = [
  "NONE" => 0,
  "SEQUENTIAL" => 1,
  "NOCACHE" => 4,
  "DEPRECATED" => 8
];
$flags = $ffi->vips_operation_get_flags($operation);
trace("flags: $flags");
foreach ($operationFlags as $name => $flag) {
  if ($flags & $flag) {
    trace("   $name");
  }
}

$argumentFlags = [
  "REQUIRED" => 1,
  "CONSTRUCT" => 2,
  "SET_ONCE" => 4,
  "SET_ALWAYS" => 8,
  "INPUT" => 16,
  "OUTPUT" => 32,
  "DEPRECATED" => 64,
  "MODIFY" => 128
];
$p_names = $ffi->new("char**[1]");
$p_flags = $ffi->new("int*[1]");
$p_n_args = $ffi->new("int[1]");
$result = $ffi->vips_object_get_args(
  FFI::cast($vipsObject, $operation),
  $p_names, 
  $p_flags, 
  $p_n_args
);
if ($result != 0) {
  error();
}
$p_names = $p_names[0];
$p_flags = $p_flags[0];
$n_args = $p_n_args[0];

trace("n_args: $n_args");

# make a hash from arg name to flags
$arguments = [];
for ($i = 0; $i < $n_args; $i++) {
  if (($p_flags[$i] & $argumentFlags["CONSTRUCT"]) != 0) {
    # libvips uses '-' to separate parts of arg names, but we
    # need '_' for php
    $name = FFI::string($p_names[$i]);
    $name = str_replace("-", "_", $name);
    $arguments[$name] = $p_flags[$i];
  }
}

// get the pspec for a property from a VipsObject 
// NULL for no such name
function get_pspec($object, $name) {
  global $ffi;
  global $vipsObject;

  $pspec = $ffi->new("GParamSpec*[1]");
  $argument_class = $ffi->new("VipsArgumentClass*[1]");
  $argument_instance = $ffi->new("VipsArgumentInstance*[1]");
  $result = $ffi->vips_object_get_argument(
    FFI::cast($vipsObject, $object),
    $name,
    $pspec, 
    $argument_class,
    $argument_instance
  );

  if ($result != 0) {
    return FFI::NULL;
  }
  else {
    return $pspec[0];
  }
}

// get the type of a property from a VipsObject
// 0 if no such property
function get_typeof($object, $name) {
  global $base_ffi;

  $pspec = get_pspec($object, $name);
  if (FFI::isNULL($pspec)) {
    # need to clear any error, this is horrible
    $base_ffi->vips_error_clear();
    return 0;
  }
  else {
    return $pspec->value_type;
  }
}

function get_blurb($object, $name) {
  global $ffi;

  $pspec = get_pspec($object, $name);
  return $ffi->g_param_spec_get_blurb($pspec);
}

# make a hash from arg name to detailed arg info
$details = [];
foreach ($arguments as $name => $flags) {
  $details[$name] = [
    "name" => $name,
    "flags" => $flags,
    "blurb" => get_blurb($operation, $name),
    "type" => get_typeof($operation, $name)
  ];
}

# split args into categories
$required_input = [];
$optional_input = [];
$required_output = [];
$optional_output = [];

foreach ($details as $name => $detail) {
  $flags = $detail["flags"];
  $blurb = $detail["blurb"];
  $type = $detail["type"];
  $typeName = $ffi->g_type_name($type);

  if (($flags & $argumentFlags["INPUT"]) &&
      ($flags & $argumentFlags["REQUIRED"]) &&
      !($flags & $argumentFlags["DEPRECATED"])) {
    $required_input[] = $name;

    # required inputs which we MODIFY are also required outputs
    if ($flags & $argumentFlags["MODIFY"]) {
      $required_output[] = $name;
    }
  }

  if (($flags & $argumentFlags["OUTPUT"]) &&
      ($flags & $argumentFlags["REQUIRED"]) &&
      !($flags & $argumentFlags["DEPRECATED"])) {
    $required_output[] = $name;
  }

  # we let deprecated optional args through, but warn about them
  # if they get used, see below
  if (($flags & $argumentFlags["INPUT"]) &&
      !($flags & $argumentFlags["REQUIRED"])) {
    $optional_input[] = $name;
  }

  if (($flags & $argumentFlags["OUTPUT"]) &&
      !($flags & $argumentFlags["REQUIRED"])) {
    $optional_output[] = $name;
  }
}

# find the first required input image arg, if any ... that will be self
$imageType = $ffi->g_type_from_name("VipsImage");
$member_x = null;
foreach ($required_input as $name) {
  $type = $details[$name]["type"];
  if ($type == $imageType) {
    $member_x = $name;
    break;
  }
}

# method args are required args, but without the image they are a
# method on
$method_args = $required_input;
if ($member_x != null) {
  $index = array_search($member_x, $method_args);
  array_splice($method_args, $index);
}

# print!
foreach ($details as $name => $detail) {
  $flags = $detail["flags"];
  $blurb = $detail["blurb"];
  $type = $detail["type"];
  $typeName = $ffi->g_type_name($type);

  trace("  $name:");

  trace("    flags: $flags");
  foreach ($argumentFlags as $name => $flag) {
    if ($flags & $flag) {
      trace("      $name");
    }
  }

  trace("    blurb: $blurb");
  trace("    type: $typeName");
}

$info = implode(", ", $required_input);
trace("required input: $info");
$info = implode(", ", $required_output);
trace("required output: $info");
$info = implode(", ", $optional_input);
trace("optional input: $info");
$info = implode(", ", $optional_output);
trace("optional output: $info");
trace("member_x: $member_x");
$info = implode(", ", $method_args);
trace("method args: $info");

// look these up in advance
$gtypes = [
  "gboolean" => $ffi->g_type_from_name("gboolean"),
  "gchararray" => $ffi->g_type_from_name("gchararray"),
  "VipsRefString" => $ffi->g_type_from_name("VipsRefString"),
  "GObject" => $ffi->g_type_from_name("GObject"),
];

// a tiny class to wrap GValue ... we need to be able to trigger g_value_unset
// when we are done with one of these, so we need to make a class

class GValue
{
  private FFI\CData $struct;
  public FFI\CData $pointer;

  function __construct() {
    global $ffi;

    # allocate a gvalue on the heap, and make it persistent between requests
    $this->struct = $ffi->new("GValue", true, true);
    $this->pointer = FFI::addr($this->struct);

    # GValue needs to be inited to all zero
    FFI::memset($this->pointer, 0, FFI::sizeof($this->struct));
  }

  function __destruct() {
    global $ffi;

    $ffi->g_value_unset($this->pointer);
  }

  function set_type(int $gtype) {
    global $ffi;

    $ffi->g_value_init($this->pointer, $gtype);
  }

  function get_type(): int {
    return $this->pointer->g_type;
  }

  function set($value) {
    global $ffi, $gtypes;

    $gtype = $this->get_type();

    switch ($gtype) {
    case $gtypes["gboolean"]:
      $ffi->g_value_set_boolean($this->pointer, $value);
      break;

    case $gtypes["gchararray"]:
      $ffi->g_value_set_string($this->pointer, $value);
      break;

    case $gtypes["VipsRefString"]:
      $ffi->vips_value_set_ref_string($this->pointer, $value);
      break;

    default:
      $fundamental = $ffi->g_type_fundamental($gtype);
      switch ($fundamental) {
      case $gtypes["GObject"]:
        break;

      default:
        trace("GValue::set(): gtype $gtype not implemented");
        exit(1);
      }
    }
  }

  function get() {
    global $ffi, $gtypes;

    $gtype = $this->get_type();
    $result = null;

    switch ($gtype) {
    case $gtypes["gboolean"]:
      $result = $ffi->g_value_get_boolean($this->pointer);
      break;

    case $gtypes["gchararray"]:
      $ffi->g_value_get_string($this->pointer);
      break;

    case $gtypes["VipsRefString"]:
      $psize = $ffi->new("size_t*");
      $result = $ffi->vips_value_get_ref_string($this->pointer, $psize);
      # $psize[0] will be the string length, but assume it's null terminated
      break;

    default:
      $fundamental = $ffi->g_type_fundamental($gtype);
      switch ($fundamental) {
      case $gtypes["GObject"]:
        # we need a class wrapping gobject before we can impement this
        trace("in get() get object");
        break;

      default:
        trace("GValue::get(): gtype $gtype not implemented");
        exit(1);
      }
    }

    return $result;
  }

}

function gobject_set($object, $name, $value) {
  global $ffi;
  global $gObject;

  $gtype = get_typeof($object, $name);

  $gvalue = new GValue();
  $gvalue->set_type($gtype);
  $gvalue->set($value);

  $gobject = FFI::cast($gObject, $object);
  $ffi->g_object_set_property($gobject, $name, $gvalue->pointer);
}

function gobject_get($object, $name) {
  global $ffi;
  global $gObject;

  $gtype = get_typeof($object, $name);

  $gvalue = new GValue();
  $gvalue->set_type($gtype);

  $gobject = FFI::cast($gObject, $object);
  $ffi->g_object_get_property($gobject, $name, $gvalue->pointer);

  return $gvalue->get();
}

// now use the info from introspection to set some parameters on $operation

trace("setting arguments ...");
gobject_set($operation, "filename", $filename);

// build the operation

trace("building ...");
$new_operation = $ffi->vips_cache_operation_build($operation);
if (FFI::isNull($new_operation)) {
  $ffi->vips_object_unref_outputs($operation);
  error();
}
$operation = $new_operation;

# need to attach input refs to output

// fetch required output args

$image = gobject_get($operation, "out");
trace("result: " . print_r($image, true));

trace("shutting down ...");
$base_ffi->vips_shutdown();
