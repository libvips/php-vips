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
     * A pointer to the underlying VipsObject.
     *
     * @internal
     */
    private \FFI\CData $pointer;

    /**
     * A pointer to the underlying GObject. This is the same as the
     * VipsObject, just cast.
     *
     * @internal
     */
    private \FFI\CData $gObject;

    function __construct($pointer)
    {
        $this->pointer = Init::ffi()->
            cast(Init::ctypes("VipsObject"), $pointer);
        $this->gObject = Init::ffi()->
            cast(Init::ctypes("GObject"), $pointer);

        parent::__construct($pointer);
    }

    // print a table of all active vipsobjects ... handy for debugging
    static function printAll() {
        Init::ffi()->vips_object_print_all();
    }

    function getDescription() {
        return Init::ffi()->vips_object_get_description($this->pointer);
    }

    // get the pspec for a property 
    // NULL for no such name
    // very slow! avoid if possible
    // FIXME add a cache for this thing
    function getPspec($name) {
        $pspec = Init::ffi()->new("GParamSpec*[1]");
        $argument_class = Init::ffi()->new("VipsArgumentClass*[1]");
        $argument_instance = Init::ffi()->new("VipsArgumentInstance*[1]");
        $result = Init::ffi()->vips_object_get_argument(
            $this->pointer,
            $name,
            $pspec, 
            $argument_class,
            $argument_instance
        );

        if ($result != 0) {
            return null;
        }
        else {
            return $pspec[0];
        }
    }

    // get the type of a property from a VipsObject
    // 0 if no such property
    function getType($name) {
        $pspec = $this->getPspec($name);
        if (\FFI::isNull($pspec)) {
            # need to clear any error, this is horrible
            Init::ffi()->vips_error_clear();
            return 0;
        }
        else {
            return $pspec->value_type;
        }
    }

    function getBlurb(string $name) {
        $pspec = $this->getPspec($name);
        return Init::ffi()->g_param_spec_get_blurb($pspec);
    }

    function getArgumentDescription(string $name) {
        $pspec = $this->getPspec($name);
        return Init::ffi()->g_param_spec_get_description($pspec);
    }

    function get(string $name) {
        $gvalue = new GValue();
        $gvalue->setType($this->getType($name));

        Init::ffi()->
            g_object_get_property($this->gObject, $name, $gvalue->pointer);
        $value = $gvalue->get();

        Utils::debugLog("get", [$name => $value]);

        return $value;
    }

    function set(string $name, $value) {
        Utils::debugLog("set", [$name => $value]);

        $gvalue = new GValue();
        $gvalue->setType($this->getType($name));
        $gvalue->set($value);

        Init::ffi()->
            g_object_set_property($this->gObject, $name, $gvalue->pointer);
    }

    function setString($string_options) {
        $result = Init::ffi()->
            vips_object_set_from_string($this->pointer, $string_options);

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
