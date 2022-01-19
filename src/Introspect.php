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

    /**
     * A hash from arg name to a hash of details.
     */
    protected mixed[] $arguments;

    /**
     * Arrays of arg names, in order and by category, eg. $this->required_input
     * = ["filename"].
     */
    protected string[] $required_input;
    protected string[] $optional_input;
    protected string[] $required_output;
    protected string[] $optional_output;

    /** 
     * The name of the arg this operation uses as "this".
     */
    protected string $member_this;

    /**
     * And the required input args, without the "this".
     */
    protected string[] $method_args;

    function __construct($name)
    {
        global $ffi;
        global $ctypes;
        global $gtypes;

        $this->name = $name;

        $pointer = $ffi->vips_operation_new($name);
        if (FFI::isNull($pointer)) {
            error();
        }
        $operation = new VipsOperation($pointer);

        $this->description = $operation->getDescription();
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

        # make a hash from arg name to flags
        $argumentFlags = [];
        for ($i = 0; $i < $n_args; $i++) {
            if (($p_flags[$i] & $argumentFlags["CONSTRUCT"]) != 0) {
                # libvips uses '-' to separate parts of arg names, but we
                # need '_' for php
                $name = FFI::string($p_names[$i]);
                $name = str_replace("-", "_", $name);
                $argumentFlags[$name] = $p_flags[$i];
            }
        }

        # make a hash from arg name to detailed arg info
        $this->arguments = [];
        foreach ($argumentFlags as $name => $flags) {
            $this->arguments[$name] = [
                "name" => $name,
                "flags" => $flags,
                "blurb" => $operation->getBlurb($operation, $name),
                "type" => $operation->getType($operation, $name)
            ];
        }

        # split args into categories
        $this->required_input = [];
        $this->optional_input = [];
        $this->required_output = [];
        $this->optional_output = [];

        foreach ($this->arguments as $name => $details) {
            $flags = $details["flags"];
            $blurb = $details["blurb"];
            $type = $details["type"];
            $typeName = $ffi->g_type_name($type);

            if (($flags & $argumentFlags["INPUT"]) &&
                ($flags & $argumentFlags["REQUIRED"]) &&
                !($flags & $argumentFlags["DEPRECATED"])) {
                $this->required_input[] = $name;

                # required inputs which we MODIFY are also required outputs
                if ($flags & $argumentFlags["MODIFY"]) {
                    $this->required_output[] = $name;
                }
            }

            if (($flags & $argumentFlags["OUTPUT"]) &&
                ($flags & $argumentFlags["REQUIRED"]) &&
                !($flags & $argumentFlags["DEPRECATED"])) {
                $this->required_output[] = $name;
            }
  
            # we let deprecated optional args through, but warn about them
            # if they get used, see below
            if (($flags & $argumentFlags["INPUT"]) &&
                !($flags & $argumentFlags["REQUIRED"])) {
                $this->optional_input[] = $name;
            }
  
            if (($flags & $argumentFlags["OUTPUT"]) &&
                !($flags & $argumentFlags["REQUIRED"])) {
                $this->optional_output[] = $name;
            }
        }

        # find the first required input image arg, if any ... that will be self
        $this->member_this = null;
        foreach ($required_input as $name) {
            $type = $details[$name]["type"];
            if ($type == $gtypes["VipsImage"]) {
                $this->member_this = $name;
                break;
            }
        }

        # method args are required args, but without the image they are a
        # method on
        $this->method_args = $this->required_input;
        if ($this->member_this != null) {
            $index = array_search($this->member_this, $this->method_args);
            array_splice($this->method_args, $index);
        }
    }

    public function __toString() {
        $result = "";

        $result .= "$this->name:\n";

        foreach ($this->arguments as $name => $details) {
            $flags = $details["flags"];
            $blurb = $details["blurb"];
            $type = $details["type"];
            $typeName = $ffi->g_type_name($type);

            $result .= "  $name:\n";

            $result .= "    flags: $flags\n";
            foreach ($argumentFlags as $name => $flag) {
                if ($flags & $flag) {
                    $resut .= "      $name\n";
                }
            }

            $result .= "    blurb: $blurb\n";
            $result .= "    type: $typeName\n";
        }

        $info = implode(", ", $this->required_input);
        $result .= "required input: $info\n";
        $info = implode(", ", $this->required_output);
        $result .= "required output: $info\n";
        $info = implode(", ", $this->optional_input);
        $result .= "optional input: $info\n";
        $info = implode(", ", $this->optional_output);
        $result .= "optional output: $info\n";
        $result .= "member_this: $this->member_this\n";
        $info = implode(", ", $this->method_args);
        $result .= "method args: $info\n";

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
