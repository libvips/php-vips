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

    public function __construct()
    {
        # allocate a gvalue on the heap, and make it persistent between requests
        $this->struct = Config::ffi()->new("GValue", true, true);
        $this->pointer = \FFI::addr($this->struct);

        # GValue needs to be inited to all zero
        \FFI::memset($this->pointer, 0, \FFI::sizeof($this->struct));
    }

    /* Turn a string into an enum value, if possible
     */
    public static function toEnum($gtype, $value)
    {
        if (is_string($value)) {
            $enum_value = Config::ffi()->
                vips_enum_from_nick("php-vips", $gtype, $value);
            if ($enum_value < 0) {
                echo "gtype = " . $gtype . "\n";
                echo "value = " . $value . "\n";
                Config::error();
            }
        } else {
            $enum_value = $value;
        }

        return $enum_value;
    }

    public static function fromEnum($gtype, $value)
    {
        $result = Config::ffi()->vips_enum_nick($gtype, $value);
        if ($result === null) {
            Config::error("value not in enum");
        }

        return $result;
    }

    public function __destruct()
    {
        Config::ffi()->g_value_unset($this->pointer);
    }

    public function setType(int $gtype)
    {
        Config::ffi()->g_value_init($this->pointer, $gtype);
    }

    public function getType(): int
    {
        return $this->pointer->g_type;
    }

    public function set($value)
    {
        $gtype = $this->getType();

        switch ($gtype) {
            case Config::gtypes("gboolean"):
                Config::ffi()->g_value_set_boolean($this->pointer, $value);
                break;

            case Config::gtypes("gint"):
                Config::ffi()->g_value_set_int($this->pointer, $value);
                break;

            case Config::gtypes("gint64"):
                Config::ffi()->g_value_set_int64($this->pointer, $value);
                break;

            case Config::gtypes("guint64"):
                Config::ffi()->g_value_set_uint64($this->pointer, $value);
                break;

            case Config::gtypes("gdouble"):
                Config::ffi()->g_value_set_double($this->pointer, $value);
                break;

            case Config::gtypes("gchararray"):
                Config::ffi()->g_value_set_string($this->pointer, $value);
                break;

            case Config::gtypes("VipsRefString"):
                Config::ffi()->
                    vips_value_set_ref_string($this->pointer, $value);
                break;

            case Config::gtypes("VipsArrayInt"):
                if (!is_array($value)) {
                    $value = [$value];
                }
                $n = count($value);
                $ctype = \FFI::arrayType(\FFI::type("int"), [$n]);
                $array = \FFI::new($ctype);
                for ($i = 0; $i < $n; $i++) {
                    $array[$i] = $value[$i];
                }
                Config::ffi()->
                    vips_value_set_array_int($this->pointer, $array, $n);
                break;

            case Config::gtypes("VipsArrayDouble"):
                if (!is_array($value)) {
                    $value = [$value];
                }
                $n = count($value);
                $ctype = \FFI::arrayType(\FFI::type("double"), [$n]);
                $array = \FFI::new($ctype);
                for ($i = 0; $i < $n; $i++) {
                    $array[$i] = $value[$i];
                }
                Config::ffi()->
                    vips_value_set_array_double($this->pointer, $array, $n);
                break;

            case Config::gtypes("VipsArrayImage"):
                if (!is_array($value)) {
                    $value = [$value];
                }
                $n = count($value);
                Config::ffi()->vips_value_set_array_image($this->pointer, $n);
                $array = Config::ffi()->
                    vips_value_get_array_image($this->pointer, null);
                for ($i = 0; $i < $n; $i++) {
                    $image = $value[$i];
                    $array[$i] = $image->pointer;
                    $image->ref();
                }
                break;

            case Config::gtypes("VipsBlob"):
                # we need to set the blob to a copy of the data that vips_lib
                # can own and free
                $n = strlen($value);
                $ctype = \FFI::arrayType(\FFI::type("char"), [$n]);
                $memory = \FFI::new($ctype, false, true);
                for ($i = 0; $i < $n; $i++) {
                    $memory[$i] = $value[$i];
                }
                Config::ffi()->
                    vips_value_set_blob_free($this->pointer, $memory, $n);
                break;

            default:
                $fundamental = Config::ffi()->g_type_fundamental($gtype);
                switch ($fundamental) {
                    case Config::gtypes("GObject"):
                        Config::ffi()->
                            g_value_set_object($this->pointer, $value->pointer);
                        break;

                    case Config::gtypes("GEnum"):
                        Config::ffi()->g_value_set_enum(
                            $this->pointer,
                            self::toEnum($gtype, $value)
                        );
                        break;

                    case Config::gtypes("GFlags"):
                        /* Just set as int.
                         */
                        Config::ffi()->
                            g_value_set_flags($this->pointer, $value);
                        break;

                    default:
                        $typeName = Config::ffi()->g_type_name($gtype);
                        throw new \BadMethodCallException(
                            "gtype $typeName ($gtype) not implemented"
                        );
                        break;
                }
        }
    }

    public function get()
    {
        $gtype = $this->getType();
        $result = null;

        switch ($gtype) {
            case Config::gtypes("gboolean"):
                $result = Config::ffi()->g_value_get_boolean($this->pointer);
                break;

            case Config::gtypes("gint"):
                $result = Config::ffi()->g_value_get_int($this->pointer);
                break;

            case Config::gtypes("gint64"):
                $result = Config::ffi()->g_value_get_int64($this->pointer);
                break;

            case Config::gtypes("guint64"):
                $result = Config::ffi()->g_value_get_uint64($this->pointer);
                break;

            case Config::gtypes("gdouble"):
                $result = Config::ffi()->g_value_get_double($this->pointer);
                break;

            case Config::gtypes("gchararray"):
                $result = Config::ffi()->g_value_get_string($this->pointer);
                break;

            case Config::gtypes("VipsRefString"):
                $p_size = Config::ffi()->new("size_t[1]");
                $result = Config::ffi()->
                    vips_value_get_ref_string($this->pointer, $p_size);
                # $p_size[0] will be the string length, but assume it's null
                # terminated
                break;

            case Config::gtypes("VipsImage"):
                $pointer = Config::ffi()->g_value_get_object($this->pointer);
                $result = new Image($pointer);
                // get_object does not increment the ref count
                $result->ref();
                break;

            case Config::gtypes("VipsArrayInt"):
                $p_len = Config::ffi()->new("int[1]");
                $pointer = Config::ffi()->
                    vips_value_get_array_int($this->pointer, $p_len);
                $result = [];
                for ($i = 0; $i < $p_len[0]; $i++) {
                    $result[] = $pointer[$i];
                }
                break;

            case Config::gtypes("VipsArrayDouble"):
                $p_len = Config::ffi()->new("int[1]");
                $pointer = Config::ffi()->
                    vips_value_get_array_double($this->pointer, $p_len);
                $result = [];
                for ($i = 0; $i < $p_len[0]; $i++) {
                    $result[] = $pointer[$i];
                }
                break;

            case Config::gtypes("VipsArrayImage"):
                $p_len = Config::ffi()->new("int[1]");
                $pointer = Config::ffi()->
                    vips_value_get_array_image($this->pointer, $p_len);
                $result = [];
                for ($i = 0; $i < $p_len[0]; $i++) {
                    $image = new Image($pointer[$i]);
                    $image->ref();
                    $result[] = $image;
                }
                break;

            case Config::gtypes("VipsBlob"):
                $p_len = Config::ffi()->new("size_t[1]");
                $pointer = Config::ffi()->
                    vips_value_get_blob($this->pointer, $p_len);
                $result = \FFI::string($pointer, $p_len[0]);
                break;

            default:
                $fundamental = Config::ffi()->g_type_fundamental($gtype);
                switch ($fundamental) {
                    case Config::gtypes("GEnum"):
                        $result = Config::ffi()->
                            g_value_get_enum($this->pointer);
                        $result = self::fromEnum($gtype, $result);
                        break;

                    case Config::gtypes("GFlags"):
                        /* Just get as int.
                         */
                        $result = Config::ffi()->
                            g_value_get_flags($this->pointer);
                        break;

                    default:
                        $typeName = Config::ffi()->g_type_name($gtype);
                        throw new \BadMethodCallException(
                            "gtype $typeName ($gtype) not implemented"
                        );
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
