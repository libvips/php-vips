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
        $this->struct = FFI::gobject()->new("GValue", true, true);
        $this->pointer = \FFI::addr($this->struct);

        # GValue needs to be inited to all zero
        \FFI::memset($this->pointer, 0, \FFI::sizeof($this->struct));
    }

    /**
     * Turn a string into an enum value, if possible
     * @throws Exception
     */
    public static function toEnum(int $gtype, $value): int
    {
        if (is_string($value)) {
            $enum_value = FFI::vips()->
                vips_enum_from_nick("php-vips", $gtype, $value);
            if ($enum_value < 0) {
                throw new Exception();
            }
        } else {
            $enum_value = $value;
        }

        return $enum_value;
    }

    /**
     * Turn an enum into a string, if possible
     * @throws Exception
     */
    public static function fromEnum(int $gtype, int $value): string
    {
        $result = FFI::vips()->vips_enum_nick($gtype, $value);
        if ($result === null) {
            throw new Exception("value not in enum");
        }

        return $result;
    }

    public function __destruct()
    {
        FFI::gobject()->g_value_unset($this->pointer);
    }

    public function setType(int $gtype): void
    {
        FFI::gobject()->g_value_init($this->pointer, $gtype);
    }

    public function getType(): int
    {
        return $this->pointer->g_type;
    }

    /**
     * Set a GValue.
     *
     * @param mixed $value Value to be set.
     *
     * @throws Exception
     */
    public function set($value): void
    {
        $gtype = $this->getType();

        switch ($gtype) {
            case FFI::gtypes("gboolean"):
                FFI::gobject()->g_value_set_boolean($this->pointer, $value);
                break;

            case FFI::gtypes("gint"):
                FFI::gobject()->g_value_set_int($this->pointer, $value);
                break;

            case FFI::gtypes("gint64"):
                FFI::gobject()->g_value_set_int64($this->pointer, $value);
                break;

            case FFI::gtypes("guint64"):
                FFI::gobject()->g_value_set_uint64($this->pointer, $value);
                break;

            case FFI::gtypes("gdouble"):
                FFI::gobject()->g_value_set_double($this->pointer, $value);
                break;

            case FFI::gtypes("gchararray"):
                FFI::gobject()->g_value_set_string($this->pointer, $value);
                break;

            case FFI::gtypes("VipsRefString"):
                FFI::vips()->
                    vips_value_set_ref_string($this->pointer, $value);
                break;

            case FFI::gtypes("VipsArrayInt"):
                if (!is_array($value)) {
                    $value = [$value];
                }
                $n = count($value);
                $array = FFI::vips()->new("int[$n]");
                for ($i = 0; $i < $n; $i++) {
                    $array[$i] = $value[$i];
                }
                FFI::vips()->
                    vips_value_set_array_int($this->pointer, $array, $n);
                break;

            case FFI::gtypes("VipsArrayDouble"):
                if (!is_array($value)) {
                    $value = [$value];
                }
                $n = count($value);
                $array = FFI::vips()->new("double[$n]");
                for ($i = 0; $i < $n; $i++) {
                    $array[$i] = $value[$i];
                }
                FFI::vips()->
                    vips_value_set_array_double($this->pointer, $array, $n);
                break;

            case FFI::gtypes("VipsArrayImage"):
                if (!is_array($value)) {
                    $value = [$value];
                }
                $n = count($value);
                FFI::vips()->vips_value_set_array_image($this->pointer, $n);
                $array = FFI::vips()->
                    vips_value_get_array_image($this->pointer, null);
                for ($i = 0; $i < $n; $i++) {
                    $image = $value[$i];
                    $array[$i] = $image->pointer;
                    $image->ref();
                }
                break;

            case FFI::gtypes("VipsBlob"):
                # we need to set the blob to a copy of the data that vips_lib
                # can own and free
                $n = strlen($value);
                $memory = FFI::vips()->new("char[$n]", false, true);
                \FFI::memcpy($memory, $value, $n);
                FFI::vips()->
                    vips_value_set_blob_free($this->pointer, $memory, $n);
                break;

            default:
                $fundamental = FFI::gobject()->g_type_fundamental($gtype);
                switch ($fundamental) {
                    case FFI::gtypes("GObject"):
                        FFI::gobject()->
                            g_value_set_object($this->pointer, $value->pointer);
                        break;

                    case FFI::gtypes("GEnum"):
                        FFI::gobject()->g_value_set_enum(
                            $this->pointer,
                            self::toEnum($gtype, $value)
                        );
                        break;

                    case FFI::gtypes("GFlags"):
                        /* Just set as int.
                         */
                        FFI::gobject()->
                            g_value_set_flags($this->pointer, $value);
                        break;

                    default:
                        $typeName = FFI::gobject()->g_type_name($gtype);
                        throw new \BadMethodCallException(
                            "gtype $typeName ($gtype) not implemented"
                        );
                }
        }
    }

    /**
     * Get the contents of a GValue.
     *
     * @return mixed The contents of this GValue.
     *
     * @throws Exception
     */
    public function get()
    {
        $gtype = $this->getType();
        $result = null;

        switch ($gtype) {
            case FFI::gtypes("gboolean"):
                $result = FFI::gobject()->g_value_get_boolean($this->pointer);
                break;

            case FFI::gtypes("gint"):
                $result = FFI::gobject()->g_value_get_int($this->pointer);
                break;

            case FFI::gtypes("gint64"):
                $result = FFI::gobject()->g_value_get_int64($this->pointer);
                break;

            case FFI::gtypes("guint64"):
                $result = FFI::gobject()->g_value_get_uint64($this->pointer);
                break;

            case FFI::gtypes("gdouble"):
                $result = FFI::gobject()->g_value_get_double($this->pointer);
                break;

            case FFI::gtypes("gchararray"):
                $result = FFI::gobject()->g_value_get_string($this->pointer);
                break;

            case FFI::gtypes("VipsRefString"):
                $p_size = FFI::vips()->new("size_t[1]");
                $result = FFI::vips()->
                    vips_value_get_ref_string($this->pointer, $p_size);
                # $p_size[0] will be the string length, but assume it's null
                # terminated
                break;

            case FFI::gtypes("VipsImage"):
                $pointer = FFI::gobject()->g_value_get_object($this->pointer);
                $result = new Image($pointer);
                // get_object does not increment the ref count
                $result->ref();
                break;

            case FFI::gtypes("VipsArrayInt"):
                $p_len = FFI::vips()->new("int[1]");
                $pointer = FFI::vips()->
                    vips_value_get_array_int($this->pointer, $p_len);
                $result = [];
                for ($i = 0; $i < $p_len[0]; $i++) {
                    $result[] = $pointer[$i];
                }
                break;

            case FFI::gtypes("VipsArrayDouble"):
                $p_len = FFI::vips()->new("int[1]");
                $pointer = FFI::vips()->
                    vips_value_get_array_double($this->pointer, $p_len);
                $result = [];
                for ($i = 0; $i < $p_len[0]; $i++) {
                    $result[] = $pointer[$i];
                }
                break;

            case FFI::gtypes("VipsArrayImage"):
                $p_len = FFI::vips()->new("int[1]");
                $pointer = FFI::vips()->
                    vips_value_get_array_image($this->pointer, $p_len);
                $result = [];
                for ($i = 0; $i < $p_len[0]; $i++) {
                    $image = new Image($pointer[$i]);
                    $image->ref();
                    $result[] = $image;
                }
                break;

            case FFI::gtypes("VipsBlob"):
                $p_len = FFI::vips()->new("size_t[1]");
                $pointer = FFI::vips()->
                    vips_value_get_blob($this->pointer, $p_len);
                $result = \FFI::string($pointer, $p_len[0]);
                break;

            default:
                $fundamental = FFI::gobject()->g_type_fundamental($gtype);
                switch ($fundamental) {
                    case FFI::gtypes("GEnum"):
                        $result = FFI::gobject()->
                            g_value_get_enum($this->pointer);
                        $result = self::fromEnum($gtype, $result);
                        break;

                    case FFI::gtypes("GFlags"):
                        /* Just get as int.
                         */
                        $result = FFI::gobject()->
                            g_value_get_flags($this->pointer);
                        break;

                    default:
                        $typeName = FFI::gobject()->g_type_name($gtype);
                        throw new \BadMethodCallException(
                            "gtype $typeName ($gtype) not implemented"
                        );
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
