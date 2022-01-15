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
 * Introspect a VIpsOperation and discover everything we can. This is called
 * on demand once per operation and the results held in a cache.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/libvips/php-vips
 */
class Introspection
{
    /**
     * The operation nickname (eg. "add").
     */
    protected string $name;

    /**
     * The operation description (eg. "add two images").
     */
    protected string $description;

    /**
     * The operation flags (eg. SEQUENTIAL | DEPRECATED).
     */
    protected int $flags;

    function __construct($name)
    {
        global $ffi;
        global $ctypes;

        $this->name = $name;

        $operation = $ffi->vips_operation_new($name);
        if (FFI::isNull($operation)) {
            error();
        }

        $this->description = $ffi->vips_object_get_description(
            FFI::cast($ctypes["VipsObject"], $operation));
        $flags = $ffi->vips_operation_get_flags($operation);

        $p_names = $ffi->new("char**[1]");
        $p_flags = $ffi->new("int*[1]");
        $p_n_args = $ffi->new("int[1]");
        $result = $ffi->vips_object_get_args(
            FFI::cast($ctypes["VipsObject"], $operation),
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



        $operation = new
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
