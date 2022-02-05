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

        case Init::gtypes("gchararray"):
            Init::ffi()->g_value_set_string($this->pointer, $value);
            break;

        case Init::gtypes("VipsRefString"):
            Init::ffi()->vips_value_set_ref_string($this->pointer, $value);
            break;

        default:
            $fundamental = Init::ffi()->g_type_fundamental($gtype);
            switch ($fundamental) {
            case Init::gtypes("GObject"):
                break;

            default:
                $typeName = Init::ffi()->g_type_name($gtype);
                throw new \BadMethodCallException("$typeName not implemented");
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

        case Init::gtypes("gchararray"):
            Init::ffi()->g_value_get_string($this->pointer);
            break;

        case Init::gtypes("VipsRefString"):
            $psize = Init::ffi()->new("size_t*");
            $result = Init::ffi()->vips_value_get_ref_string($this->pointer, $psize);
            # $psize[0] will be the string length, but assume it's null 
            # terminated
            break;

        default:
            $fundamental = Init::ffi()->g_type_fundamental($gtype);
            switch ($fundamental) {
            case Init::gtypes("GObject"):
                # we need a class wrapping gobject before we can impement this
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
