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
 * @package   Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @version   GIT:ad44dfdd31056a41cbf217244ce62e7a702e0282
 * @link      https://github.com/jcupitt/php-vips
 */

namespace Vips\Image;

if (!extension_loaded("vips")) {
    if (!dl('vips.' . PHP_SHLIB_SUFFIX)) {
        echo "vips: unable to load vips extension\n"; 
    }
}

/**
 * Image represents an image. 
 *
 * @category  Images
 * @package   Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @version   Release:0.1.0
 * @link      https://github.com/jcupitt/php-vips
 */
class Image implements \ArrayAccess
{
    /* The resource for the underlying VipsImage.
     */
    private $_image;

    /**
     * Wrap a Vips\Image around an underlying vips resource. 
     *
     * Don't call this yourself, users should stick to (for example)
     * Vips\Image::newFromFile().
     *
     * @param resource $image The underlying vips image resource that this
     *  class should wrap.
     */
    public function __construct($image) 
    {
        $this->_image = $image;
    }

    /**
     * Apply a func to every numeric member of $value. Useful for self::subtract
     * etc.
     *
     * @param mixed    $value The thing we walk.
     * @param function $func  Apply this.
     *
     * @return mixed The updated $value.
     */ 
    private static function _mapNumeric($value, $func)
    {
        if (is_numeric($value)) {
            $value = $func($value);
        } else if (is_array($value)) {
            array_walk_recursive(
                $value, function (&$item, $key) use ($func) {
                    $item = self::_mapNumeric($item, $func);
                }
            );
        }

        return $value;
    }

    /**
     * Is a $value a rectangular 2D array?
     *
     * @param mixed $value Test this.
     *
     * @return bool true if this is a 2D array.
     */
    static private function _is2D($value)
    {
        if (!is_array($value)) {
            return false;
        }

        $height = count($value);
        if (!is_array($value[0])) {
            return false;
        }
        $width = count($value[0]);

        foreach ($array as $row) {
            if (!is_array($row) || count($row) != $width) { 
                return false;
            }
        }

        return true;
    }

    /**
     * Turn a constant (eg. 1, "12", [1, 2, 3], [[1]]) into an image using
     * match_image as a guide.
     *
     * @param Image $match_image Use this image as a guide. 
     * @param mixed $value       Turn this into an image.
     *
     * @return Image The image we created. 
     */
    private static function _imageize($match_image, $value)
    {
        if (self::_is2D($value)) {
            $result = self::newFromArray($value);
        } else {
            $pixel = self::black(1, 1)->add($value)->cast($match_image->format);
            $result = $pixel->embed(
                0, 0, 
                $match_image->width, $match_image->height,
                ["extend" => "copy"]
            );
            $result->interpretation = $match_image->interpretation; 
        }

        return $result;
    }

    /**
     * Unwrap an array of stuff ready to pass down to the vips_ layer. We
     * swap instances of the Vips\Image for the plain resource.
     *
     * @param mixed $result Unwrap this.
     *
     * @return mixed $result unwrapped, ready for vips.
     */
    private static function _unwrap($result) 
    {
        array_walk_recursive(
            $result, function (&$item, $key) {
                if ($item instanceof Image) {
                    $item = $item->_image;
                }
            }
        );

        return $result;
    }

    /**
     * Is $value a VipsImage.
     * 
     * @param mixed $value The thing to test.
     *
     * @return bool true if this is a vips image resource.
     */
    private static function _isImage($value)
    {
            return is_resource($value) &&
                get_resource_type($value) == "GObject";
    }

    /**
     * Wrap up the result of a vips_ call ready to erturn it to PHP. We do 
     * two things:
     *
     * - If the array is a singleton, we strip it off. For example, many 
     *   operations return a single result and there's no sense handling 
     *   this as an array of values, so we transform ["out" => x] -> x. 
     *
     * - Any VipsImage resources are rewrapped as instances of Vips\Image.
     *
     * @param mixed $result Wrap this up.
     *
     * @return mixed $result, but wrapped up as a php class.
     */
    private static function _wrap($result) 
    {
        if (!is_array($result)) {
            $result = ["x" => $result];
        }

        array_walk_recursive(
            $result, function (&$item) {
                if (self::_isImage($item)) { 
                    $item = new Image($item);
                }
            }
        );

        if (count($result) == 1) {
            $result = array_shift($result);
        }

        return $result;
    }

    /** 
     * Create a new Vips\Image from a file on disc.
     *
     * @param string $filename The file to open.
     * @param array  $options  Any options to pass on to the load operation.
     *
     * @return A new Vips\Image.
     */
    public static function newFromFile($filename, $options = []) 
    {
        $options = self::_unwrap($options);
        $result = vips_image_new_from_file($filename, $options);
        return self::_wrap($result);
    }

    /** 
     * Create a new Vips\Image from a compressed image held as a string. 
     *
     * @param string $buffer        The formatted image to open.
     * @param string $option_string Any text-style options to pass to the
     *     selected loader. 
     * @param array  $options       Any options to pass on to the load operation.
     *
     * @return A new Vips\Image.
     */
    public static function newFromBuffer($buffer, 
        $option_string = "", $options = []
    ) {
        $options = self::_unwrap($options);
        $result = vips_image_new_from_buffer($buffer, $option_string, $options);
        return self::_wrap($result);
    }

    /** 
     * Create a new Vips\Image from a php array. 
     *
     * 2D arrays become 2D images. 1D arrays become 2D images with height 1. 
     *
     * @param array  $array  The array to make the image from. 
     * @param double $scale  The "scale" metadata item. Useful for integer
     *     convolution masks.
     * @param double $offset The "offset" metadata item. Useful for integer
     *     convolution masks.
     *
     * @return Image A new Vips\Image.
     */
    public static function newFromArray($array, $scale = 1, $offset = 0) 
    {
        $result = vips_image_new_from_array($array, $scale, $offset);
        return self::_wrap($result);
    }

    /** 
     * Write an image to a file. 
     *
     * @param string $filename The file to write the image to.
     * @param array  $options  Any options to pass on to the selected save 
     * operation.
     *
     * @return bool true for success and false for failure.
     */
    public function writeToFile($filename, $options = []) 
    {
        $options = self::_unwrap($options);
        $result = vips_image_write_to_file($this->_image, $filename, $options);
        return self::_wrap($result);
    }

    /** 
     * Write an image to a formatted string. 
     *
     * @param string $suffix  The file type suffix, eg. ".jpg".
     * @param array  $options Any options to pass on to the selected save 
     * operation.
     *
     * @return string The formatted image. 
     */
    public function writeToBuffer($suffix, $options = []) 
    {
        $options = self::_unwrap($options);
        $result = vips_image_write_to_buffer($this->_image, $suffix, $options);
        return self::_wrap($result);
    }

    /**
     * Get any property from the underlying image.
     * 
     * @param string $name The property name. 
     *
     * @return mixed
     */
    public function __get($name) 
    {
        $result = vips_image_get($this->_image, $name);
        return self::_wrap($result);
    }

    /**
     * Set any property on the underlying image.
     * 
     * @param string $name  The property name. 
     * @param mixed  $value The value to set for this property.
     *
     * @return void
     */
    public function __set($name, $value) 
    {
        vips_image_set($this->_image, $name, $value);
    }

    /**
     * Call any vips operation.
     *
     * @param string  $name      The operation name. 
     * @param Image   $instance  The instance this operation is being 
     *     invoked from.
     * @param mixed[] $arguments An array of arguments to pass to the operation.
     *
     * @return mixed The result(s) of the operation. 
     */
    public static function call($name, $instance, $arguments) 
    {
        /*
        echo "call: ", $name, "\n"; 
        echo "instance = ";
        var_dump($instance);
        echo "arguments = ";
        var_dump($arguments);
         */

        $arguments = array_merge([$name, $instance], $arguments);
        $arguments = self::_unwrap($arguments);
        $result = call_user_func_array("vips_call", $arguments);
        return self::_wrap($result);
    }

    /**
     * Handy for things like self::more. Call a 2-ary vips operator like
     * "more", but if the arg is not an image (ie. it's a constant), call
     * "more_const" instead.
     *
     * @param mixed   $other   The right-hand argument. 
     * @param string  $base    The base part of the operation name. 
     * @param string  $op      The action to invoke.
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return mixed[] The operation result.
     */
    private function _callEnum($other, $base, $op, $options = [])
    {
        if ($other instanceof Image) {
            return self::call(
                $base, $this, array_merge([$other, $op], $options)
            );
        } else {
            return self::call(
                $base . "_const", $this, array_merge([$op, $other], $options)
            );
        }
    }

    /**
     * Call any vips operation as an instance method.
     *
     * @param string  $name      The thing we call.
     * @param mixed[] $arguments The arguments to the thing.
     *
     * @return mixed The result.
     */
    public function __call($name, $arguments) 
    {
        return self::call($name, $this, $arguments); 
    }

    /**
     * Call any vips operation as a class method.
     *
     * @param string  $name      The thing we call.
     * @param mixed[] $arguments The arguments to the thing.
     *
     * @return mixed The result.
     */
    public static function __callStatic($name, $arguments) 
    {
        return self::call($name, null, $arguments); 
    }

    /**
     * Our ArrayAccess interface ... we allow [] to get band.
     *
     * @param int $offset The index to fetch.
     *
     * @return bool true if the index exists.
     */
    public function offsetExists($offset) 
    {
        return $offset >= 0 && $offset <= $this->bands - 1;
    }

    /**
     * Our ArrayAccess interface ... we allow [] to get band.
     *
     * @param int $offset The index to fetch.
     *
     * @return Image the extracted band.
     */
    public function offsetGet($offset) 
    {
        return self::offsetExists($offset) ? self::extract_band($offset) : null;
    }

    /**
     * Our ArrayAccess interface ... we allow [] to get band.
     *
     * @param int   $offset The index to set.
     * @param Image $value  The band to insert
     *
     * @return Image the expanded image.
     */
    public function offsetSet($offset, $value) 
    {
        echo "Vips\Image::offsetSet: not implemented\n"; 
    }

    /**
     * Our ArrayAccess interface ... we allow [] to get band.
     *
     * @param int $offset The index to remove.
     *
     * @return Image the reduced image.
     */
    public function offsetUnset($offset) 
    {
        echo "Vips\Image::offsetUnset: not implemented\n"; 
    }

    /**
     * Add $other to this image.
     *
     * @param mixed   $other   The thing to add to this image.
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function add($other, $options = [])
    {
        if ($other instanceof Image) {
            return self::call("add", $this, array_merge([$other], $options));
        } else {
            return self::linear(1, $other, $options); 
        }
    }

    /**
     * Subtract $other from this image.
     *
     * @param mixed   $other   The thing to subtract from this image.
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function subtract($other, $options = [])
    {
        if ($other instanceof Image) {
            return self::call(
                "subtract", $this, array_merge([$other], $options)
            );
        } else {
            $other = self::_mapNumeric(
                $other, function ($value) {
                    return -1 * $value;
                }
            );
            return self::linear(1, $other, $options);
        }
    }

    /**
     * Multiply this image by $other.
     *
     * @param mixed   $other   The thing to multiply this image by.
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function multiply($other, $options = [])
    {
        if ($other instanceof Image) {
            return self::call(
                "multiply", $this, array_merge([$other], $options)
            );
        } else {
            return self::linear($other, 0, $options);
        }
    }

    /**
     * Divide this image by $other.
     *
     * @param mixed   $other   The thing to divide this image by.
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function divide($other, $options = [])
    {
        if ($other instanceof Image) {
            return self::call(
                "divide", $this, array_merge([$other], $options)
            );
        } else {
            $other = self::_mapNumeric(
                $other, function ($value) {
                    return $value ** -1;
                }
            );
            return self::linear($other, 0, $options);
        }
    }

    /**
     * Remainder of this image and $other.
     *
     * @param mixed   $other   The thing to take the remainder with.
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function remainder($other, $options = [])
    {
        if ($other instanceof Image) {
            return self::call(
                "remainder", $this, array_merge([$other], $options)
            );
        } else {
            return self::call(
                "remainder_const", $this, array_merge([$other], $options)
            );
        }
    }

    /**
     * Find $this to the power of $other.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function pow($other, $options = [])
    {
        return self::_callEnum($other, "math2", "pow", $options);
    }

    /**
     * Find $other to the power of $this.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function wop($other, $options = [])
    {
        return self::_callEnum($other, "math2", "wop", $options);
    }

    /**
     * Shift $this left by $other.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function lshift($other, $options = [])
    {
        return self::_callEnum($other, "boolean", "lshift", $options);
    }

    /**
     * Shift $this right by $other.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function rshift($other, $options = [])
    {
        return self::_callEnum($other, "boolean", "rshift", $options);
    }

    /**
     * Bitwise AND of $this and $other.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function and($other, $options = [])
    {
        return self::_callEnum($other, "boolean", "and", $options);
    }

    /**
     * Bitwise OR of $this and $other. 
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function or($other, $options = [])
    {
        return self::_callEnum($other, "boolean", "or", $options);
    }

    /**
     * Bitwise EOR of $this and $other. 
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function eor($other, $options = [])
    {
        return self::_callEnum($other, "boolean", "eor", $options);
    }

    /**
     * 255 where $this is more than $other. 
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function more($other, $options = [])
    {
        return self::_callEnum($other, "relational", "more", $options);
    }

    /**
     * 255 where $this is more than or equal to $other.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function moreEq($other, $options = [])
    {
        return self::_callEnum($other, "relational", "moreeq", $options);
    }

    /**
     * 255 where $this is less than $other.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function less($other, $options = [])
    {
        return self::_callEnum($other, "relational", "less", $options);
    }

    /**
     * 255 where $this is less than or equal to $other.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function lessEq($other, $options = [])
    {
        return self::_callEnum($other, "relational", "lesseq", $options);
    }

    /**
     * 255 where $this is equal to $other.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function equal($other, $options = [])
    {
        return self::_callEnum($other, "relational", "equal", $options);
    }

    /**
     * 255 where $this is not equal to $other.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function notEq($other, $options = [])
    {
        return self::_callEnum($other, "relational", "noteq", $options);
    }

    /**
     * Join $this and $other bandwise.
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function bandjoin($other, $options = [])
    {
        /* Allow a single unarrayed value as well.
         */
        if (!is_array($other)) {
            $other = [$other];
        }

        /* If $other is all numbers, we can use self::bandjoin_const().
         */
        $is_const = true;
        foreach ($other as $item) {
            if (!is_numeric($item)) {
                $is_const = false;
                break;
            }
        }

        /* We can't use self::bandjoin(), that would just recurse.
         */
        if ($is_const) {
            return self::call(
                "bandjoin_const", $this, array_merge([$other], $options)
            );
        } else {
            return self::call(
                "bandjoin", null, array_merge(
                    [array_merge([$this], $other)], $options
                )
            );
        }
    }

    /**
     * Split $this into an array of single-band images.
     *
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Image[] An array of images. 
     */
    public function bandsplit($options = [])
    {
        $result = [];

        for ($i = 0; $i < $this->bands; $i++) {
            $result[] = $this->extract_band($i. $options);
        }

        return $result;
    }

    /**
     * For each band element, sort the array of input images and pick the
     * median. Use the index option to pick something else.  
     *
     * @param mixed   $other   The right-hand side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function bandrank($other, $options = [])
    {
        /* bandrank will appear as a static class member, as 
         * Vips\Image::bandrank([a, b, c]), but it's better as an instance 
         * method.
         * 
         * We need to define this by hand.
         */

        /* Allow a single unarrayed value as well.
         */
        if (!is_array($other)) {
            $other = [$other];
        }

        return self::call(
            "bandrank", null, array_merge(
                [array_merge([$this], $other)], $options
            )
        );
    }

    /**
     * Position of max is awkward with plain self::max.
     *
     * @return double, int, int The value and position of the maximum. 
     */
    public function maxpos()
    {
        $result = $this->max(["x" => true, "y" => true]);
        $out = $result["out"];
        $x = $result["x"];
        $y = $result["y"];

        return [$out, $x, $y];
    }

    /**
     * Position of min is awkward with plain self::max.
     *
     * @return double, int, int The value and position of the minimum. 
     */
    public function minpos()
    {
        $result = $this->min(["x" => true, "y" => true]);
        $out = $result["out"];
        $x = $result["x"];
        $y = $result["y"];

        return [$out, $x, $y];
    }

    /**
     * Use $this as a condition image to pick pixels from either $then or
     * $else.
     *
     * @param mixed   $then    The true side of the operator
     * @param mixed   $else    The false side of the operator. 
     * @param mixed[] $options An array of options to pass to the operation.
     *
     * @return Vips\Image A new image.
     */
    public function ifthenelse($then, $else, $options = [])
    {
        /* We need different imageize rules for this. We need $then and $else to
         * match each other first, and only if they are both constants do we
         * match to $this.
         */

        $match_image = null;
        foreach ([$then, $else, $this] as $item) {
            if ($item instanceof Image) {
                $match_image = $item;
                break;
            }
        }

        if (!($then instanceof Image)) {
            $then = self::_imageize($match_image, $then);
        }

        if (!($else instanceof Image)) {
            $else = self::_imageize($match_image, $else);
        }

        return self::call(
            "ifthenelse", $this, array_merge([$then, $else], $options)
        );
    }

    /** 
     * Return the largest integral value not greater than the argument.
     *
     * @return Vips\Image A new image.
     */
    public function floor() 
    {
        return $this->round("floor");
    }

    /** 
     * Return the smallest integral value not less than the argument.
     *
     * @return Vips\Image A new image.
     */
    public function ceil() 
    {
        return $this->round("ceil");
    }

    /** 
     * Return the nearest integral value.
     *
     * @return Vips\Image A new image.
     */
    public function rint() 
    {
        return $this->round("rint");
    }

    /** 
     * AND image bands together.
     *
     * @return Vips\Image A new image.
     */
    public function bandand() 
    {
        return $this->bandbool("and");
    }

    /** 
     * OR image bands together.
     *
     * @return Vips\Image A new image.
     */
    public function bandor() 
    {
        return $this->bandbool("or");
    }

    /** 
     * EOR image bands together.
     *
     * @return Vips\Image A new image.
     */
    public function bandeor() 
    {
        return $this->bandbool("eor");
    }

    /** 
     * Return the real part of a complex image.
     *
     * @return Vips\Image A new image.
     */
    public function real() 
    {
        return $this->complexget("real");
    }

    /** 
     * Return the imaginary part of a complex image.
     *
     * @return Vips\Image A new image.
     */
    public function imag() 
    {
        return $this->complexget("imag");
    }

    /* use this for polar() and rect()
     
    def run_cmplx(fn, image):
        """Run a complex function on a non-complex image.

        The image needs to be complex, or have an even number of bands. The input
        can be int, the output is always float or double.
        """
        original_format = image.format

        if not Vips.band_format_iscomplex(image.format):
            if image.bands % 2 != 0:
                raise "not an even number of bands"

            if not Vips.band_format_isfloat(image.format):
                image = image.cast(Vips.BandFormat.FLOAT)

            if image.format == Vips.BandFormat.DOUBLE:
                new_format = Vips.BandFormat.DPCOMPLEX
            else:
                new_format = Vips.BandFormat.COMPLEX

            image = image.copy(format = new_format, bands = image.bands / 2)

        image = fn(image)

        if not Vips.band_format_iscomplex(original_format):
            if image.format == Vips.BandFormat.DPCOMPLEX:
                new_format = Vips.BandFormat.DOUBLE
            else:
                new_format = Vips.BandFormat.FLOAT

            image = image.copy(format = new_format, bands = image.bands * 2)

        return image
     */

    /** 
     * Return an image converted to polar coordinates.
     *
     * @return Vips\Image A new image.
     */
    public function polar() 
    {
        return $this->complex("polar");
    }

    /** 
     * Return an image converted to rectangular coordinates.
     *
     * @return Vips\Image A new image.
     */
    public function rect() 
    {
        return $this->complex("rect");
    }

    /** 
     * Return the complex conjugate of an image.
     *
     * @return Vips\Image A new image.
     */
    public function conj() 
    {
        return $this->complex("conj");
    }

    /** 
     * Return the sine of an image in degrees.
     *
     * @return Vips\Image A new image.
     */
    public function sin() 
    {
        return $this->math("sin");
    }

    /** 
     * Return the cosine of an image in degrees.
     *
     * @return Vips\Image A new image.
     */
    public function cos() 
    {
        return $this->math("cos");
    }

    /** 
     * Return the tangent of an image in degrees.
     *
     * @return Vips\Image A new image.
     */
    public function tan() 
    {
        return $this->math("tan");
    }

    /** 
     * Return the inverse sine of an image in degrees.
     *
     * @return Vips\Image A new image.
     */
    public function asin() 
    {
        return $this->math("asin");
    }

    /** 
     * Return the inverse cosine of an image in degrees.
     *
     * @return Vips\Image A new image.
     */
    public function acos() 
    {
        return $this->math("acos");
    }

    /** 
     * Return the inverse tangent of an image in degrees.
     *
     * @return Vips\Image A new image.
     */
    public function atan() 
    {
        return $this->math("atan");
    }

    /** 
     * Return the natural log of an image.
     *
     * @return Vips\Image A new image.
     */
    public function log() 
    {
        return $this->math("log");
    }

    /** 
     * Return the log base 10 of an image.
     *
     * @return Vips\Image A new image.
     */
    public function log10() 
    {
        return $this->math("log10");
    }

    /** 
     * Return e ** pixel.
     *
     * @return Vips\Image A new image.
     */
    public function exp() 
    {
        return $this->math("exp");
    }

    /** 
     * Return 10 ** pixel.
     *
     * @return Vips\Image A new image.
     */
    public function exp10() 
    {
        return $this->math("exp10");
    }

    /** 
     * Erode with a structuring element.
     *
     * @param mixed $mask Erode with this structing element.
     *
     * @return Vips\Image A new image.
     */
    public function erode($mask) 
    {
        return $this->morph($mask, "erode");
    }

    /** 
     * Dilate with a structuring element.
     *
     * @param mixed $mask Dilate with this structing element.
     *
     * @return Vips\Image A new image.
     */
    public function dilate($mask) 
    {
        return $this->morph($mask, "dilate");
    }

    /** 
     * $size x $size median filter.
     *
     * @param int $size Size of median filter.
     *
     * @return Vips\Image A new image.
     */
    public function median($size) 
    {
        return $this->rank(size, size, (size * size) / 2);
    }

    /** 
     * Flip horizontally.
     *
     * @return Vips\Image A new image.
     */
    public function fliphor() 
    {
        return $this->flip("horizontal");
    }

    /** 
     * Flip vertically.
     *
     * @return Vips\Image A new image.
     */
    public function flipver() 
    {
        return $this->flip("vertical");
    }

    /** 
     * Rotate 90 degrees clockwise.
     *
     * @return Vips\Image A new image.
     */
    public function rot90() 
    {
        return $this->rot("d90");
    }

    /** 
     * Rotate 180 degrees.
     *
     * @return Vips\Image A new image.
     */
    public function rot180() 
    {
        return $this->rot("d180");
    }

    /** 
     * Rotate 270 degrees clockwise.
     *
     * @return Vips\Image A new image.
     */
    public function rot270() 
    {
        return $this->rot("d270");
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

?>
