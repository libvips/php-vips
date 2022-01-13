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

/**
 * This class holds a pointer to a VipsObject (the libvips base class) and 
 * manages properties.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/libvips/php-vips
 */
abstract class VipsObject extends GObject
{
    /**
     * A pointer to the underlying VipsObject. This is the same as the
     * GObject, just cast to VipsObject to help FFI.
     *
     * @internal
     */
    private FFI\CData $vipsObject;

    function __construct($pointer)
    {
        global $ffi;
        global $ctypes;

        $this->vipsObject = $ffi->cast($ctypes["VipsObject"], $pointer);
        parent::__construct($pointer);
    }

    // print a table of all active vipsobjects ... handy for debugging
    static function printAll() {
        global $ffi;

        $ffi->vips_object_print_all();
    }

    // get the pspec for a property 
    // NULL for no such name
    // very slow! avoid if possible
    // FIXME add a cache for this thing
    function getPspec($name) {
        global $ffi;
        global $ctypes;

        $pspec = $ffi->new("GParamSpec*[1]");
        $argument_class = $ffi->new("VipsArgumentClass*[1]");
        $argument_instance = $ffi->new("VipsArgumentInstance*[1]");
        $result = $ffi->vips_object_get_argument(
            $this->vipsObject,
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
    function getType($name) {
        global $base_ffi;

        $pspec = $this->getPspec($name);
        if (FFI::isNULL($pspec)) {
            # need to clear any error, this is horrible
            $base_ffi->vips_error_clear();
            return 0;
        }
        else {
            return $pspec->value_type;
        }
    }

    function getBlurb($name) {
        global $ffi;

        $pspec = $this->getPspec($name);
        return $ffi->g_param_spec_get_blurb($pspec);
    }

    function getDescription($name) {
        global $ffi;

        $pspec = $this->getPspec($name);
        return $ffi->g_param_spec_get_description($pspec);
    }

    function get($name) {
        global $ffi;
        global $ctypes;

        $gvalue = new GValue();
        $gvalue->setType($this->getType($name));

        $ffi->g_object_get_property($this->gObject, $name, $gvalue->pointer);

        return $gvalue->get();
    }

    function set($name, $value) {
        global $ffi;

        $gvalue = new GValue();
        $gvalue->setType($this->getType($name));
        $gvalue->set($value);

        $ffi->g_object_set_property($this->gObject, $name, $gvalue->pointer);
    }

    function setString($string_options) {
        global $ffi;

        $result = $ffi->vips_object_set_from_string(
            $this->vipsObject, 
            $string_options
        );

        return $result == 0;
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
