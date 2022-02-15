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

class GValue
{
    private \FFI\CData $struct;
    public \FFI\CData $pointer;

    function __construct() {
        # allocate a gvalue on the heap, and make it persistent between requests
        $this->struct = Init::ffi()->new("GValue", true, true);
        $this->pointer = \FFI::addr($this->struct);

        # GValue needs to be inited to all zero
        \FFI::memset($this->pointer, 0, \FFI::sizeof($this->struct));
    }

    /* Turn a string into an enum value, if possible
     */
    static function toEnum($gtype, $value) {
        if (is_string($value)) {
            $enum_value = Init::ffi()->
                vips_enum_from_nick("php-vips", $gtype, $value);
            if ($enum_value < 0) {
                Init::error();
            }
        }
        else {
            $enum_value = $value;
        }

        return $enum_value;
    }

    static function fromEnum($gtype, $value) {
        $result = Init::ffi()->vips_enum_nick($gtype, $value);
        if ($result === null) {
            Init::error("value not in enum");
        }

        return $result;
    }

    function __destruct() {
        Init::ffi()->g_value_unset($this->pointer);
    }

    function setType(int $gtype) {
        Init::ffi()->g_value_init($this->pointer, $gtype);
    }

    function getType(): int {
        return $this->pointer->g_type;
    }

    function set($value) {
        $gtype = $this->getType();

        switch ($gtype) {
        case Init::gtypes("gboolean"):
            Init::ffi()->g_value_set_boolean($this->pointer, $value);
            break;

        case Init::gtypes("gint"):
            Init::ffi()->g_value_set_int($this->pointer, $value);
            break;

        case Init::gtypes("gint64"):
            Init::ffi()->g_value_set_int64($this->pointer, $value);
            break;

        case Init::gtypes("guint64"):
            Init::ffi()->g_value_set_uint64($this->pointer, $value);
            break;

        case Init::gtypes("gdouble"):
            Init::ffi()->g_value_set_double($this->pointer, $value);
            break;

        case Init::gtypes("gchararray"):
            Init::ffi()->g_value_set_string($this->pointer, $value);
            break;

        case Init::gtypes("VipsRefString"):
            Init::ffi()->vips_value_set_ref_string($this->pointer, $value);
            break;

        case Init::gtypes("VipsArrayInt"):
            if (!is_array($value)) {
                $value = [$value];
            }
            $n = count($value);
            $ctype = \FFI::arrayType(\FFI::type("int"), [$n]);
            $array = \FFI::new($ctype);
            for ($i = 0; $i < $n; $i++) {
                $array[$i] = $value[$i];
            }
            Init::ffi()->vips_value_set_array_int($this->pointer, $array, $n);
            break;

        case Init::gtypes("VipsArrayDouble"):
            if (!is_array($value)) {
                $value = [$value];
            }
            $n = count($value);
            $ctype = \FFI::arrayType(\FFI::type("double"), [$n]);
            $array = \FFI::new($ctype);
            for ($i = 0; $i < $n; $i++) {
                $array[$i] = $value[$i];
            }
            Init::ffi()->
                vips_value_set_array_double($this->pointer, $array, $n);
            break;

        case Init::gtypes("VipsArrayImage"):
            if (!is_array($value)) {
                $value = [$value];
            }
            $n = count($value);
            Init::ffi()->vips_value_set_array_image($this->pointer, $n);
            $array = Init::ffi()->
                vips_value_get_array_image($this->pointer, NULL);
            for ($i = 0; $i < $n; $i++) {
                $pointer = $value[$i]->pointer;
                $array[$i] = $pointer;
                Init::ffi()->g_object_ref($pointer);
            }
            break;

        case Init::gtypes("VipsBlob"):
            # we need to set the blob to a copy of the data that vips_lib
            # can own and free
            $n = strlen($value);
            $ctype = \FFI::arrayType(\FFI::type("char"), [$n]);
            $memory = \FFI::new($ctype, false, true);
            for ($i = 0; $i < $n; $i++) {
                $memory[$i] = $value[$i];
            }
            Init::ffi()->
                vips_value_set_blob_free($this->pointer, $memory, $n);
            break;

        default:
            $fundamental = Init::ffi()->g_type_fundamental($gtype);
            switch ($fundamental) {
            case Init::gtypes("GObject"):
                Init::ffi()->
                    g_value_set_object($this->pointer, $value->pointer);
                break;

            case Init::gtypes("GEnum"):
                Init::ffi()->g_value_set_enum($this->pointer, 
                    self::toEnum($gtype, $value));
                break;

            case Init::gtypes("GFlags"):
                /* Just set as int.
                 */
                Init::ffi()->g_value_set_flags($this->pointer, $value);
                break;

            default:
                $typeName = Init::ffi()->g_type_name($gtype);
                throw new \BadMethodCallException("gtype $gtype not implemented");
                break;
            }
        }
    }

    function get() {
        $gtype = $this->getType();
        $result = null;

        switch ($gtype) {
        case Init::gtypes("gboolean"):
            $result = Init::ffi()->g_value_get_boolean($this->pointer);
            break;

        case Init::gtypes("gint"):
            $result = Init::ffi()->g_value_get_int($this->pointer);
            break;

        case Init::gtypes("gint64"):
            $result = Init::ffi()->g_value_get_int64($this->pointer);
            break;

        case Init::gtypes("guint64"):
            $result = Init::ffi()->g_value_get_uint64($this->pointer);
            break;

        case Init::gtypes("gdouble"):
            $result = Init::ffi()->g_value_get_double($this->pointer);
            break;

        case Init::gtypes("gchararray"):
            $result = Init::ffi()->g_value_get_string($this->pointer);
            break;

        case Init::gtypes("VipsRefString"):
            $p_size = Init::ffi()->new("size_t[1]");
            $result = Init::ffi()->
                vips_value_get_ref_string($this->pointer, $p_size);
            # $p_size[0] will be the string length, but assume it's null 
            # terminated
            break;

        case Init::gtypes("VipsImage"):
            $pointer = Init::ffi()->g_value_get_object($this->pointer);
            // get_object does not increment the ref count 
            Init::ffi()->g_object_ref($pointer);
            $result = new Image($pointer);
            break;

        case Init::gtypes("VipsArrayInt"):
            $p_len = Init::ffi()->new("int[1]");
            $pointer = Init::ffi()->
                vips_value_get_array_int($this->pointer, $p_len);
            $result = [];
            for ($i = 0; $i < $p_len[0]; $i++) {
                $result[] = $pointer[$i];
            }
            break;

        case Init::gtypes("VipsArrayDouble"):
            $p_len = Init::ffi()->new("int[1]");
            $pointer = Init::ffi()->
                vips_value_get_array_double($this->pointer, $p_len);
            $result = [];
            for ($i = 0; $i < $p_len[0]; $i++) {
                $result[] = $pointer[$i];
            }
            break;

        case Init::gtypes("VipsArrayImage"):
            $p_len = Init::ffi()->new("int[1]");
            $pointer = Init::ffi()->
                vips_value_get_array_image($this->pointer, $p_len);
            $result = [];
            for ($i = 0; $i < $p_len[0]; $i++) {
                $image_pointer = $pointer[$i];
                Init::ffi()->g_object_ref($image_pointer);
                $result[] = new Image($image_pointer);
            }
            break;

        case Init::gtypes("VipsBlob"):
            $p_len = Init::ffi()->new("size_t[1]");
            $pointer = Init::ffi()->
                vips_value_get_blob($this->pointer, $p_len);
            $result = \FFI::string($pointer, $p_len[0]);
            break;

        default:
            $fundamental = Init::ffi()->g_type_fundamental($gtype);
            switch ($fundamental) {
            case Init::gtypes("GEnum"):
                $result = Init::ffi()->g_value_get_enum($this->pointer); 
                $result = self::fromEnum($gtype, $result);
                break;

            case Init::gtypes("GFlags"):
                /* Just get as int.
                 */
                $result = Init::ffi()->g_value_get_flags($this->pointer);
                break;

            default:
                $typeName = Init::ffi()->g_type_name($gtype);
                throw new \BadMethodCallException("$typeName not implemented");
                break;
            }
        }

        return $result;
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
