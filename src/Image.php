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
 * @version   GIT:ad44dfdd31056a41cbf217244ce62e7a702e0282
 * @link      https://github.com/jcupitt/php-vips
 */

namespace Jcupitt\Vips;

if (!extension_loaded("vips")) {
    if (!dl('vips.' . PHP_SHLIB_SUFFIX)) {
        echo "vips: unable to load vips extension\n";
    }
}

/**
 * Image represents an image.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @version   Release:0.1.2
 * @link      https://github.com/jcupitt/php-vips
 */
class Image implements \ArrayAccess
{
    /**
     * The resource for the underlying VipsImage.
     */
    private $_image;

    /**
     * Wrap a Image around an underlying vips resource.
     *
     * Don't call this yourself, users should stick to (for example)
     * Image::newFromFile().
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
     * @param \Closure $func  Apply this.
     *
     * @return mixed The updated $value.
     */
    private static function _mapNumeric($value, \Closure $func)
    {
        if (is_numeric($value)) {
            $value = $func($value);
        } else {
            if (is_array($value)) {
                array_walk_recursive(
                    $value, function (&$item, $key) use ($func) {
                        $item = self::_mapNumeric($item, $func);
                    }
                );
            }
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
    static private function _is2D($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (!is_array($value[0])) {
            return false;
        }
        $width = count($value[0]);

        foreach ($value as $row) {
            if (!is_array($row) || count($row) != $width) {
                return false;
            }
        }

        return true;
    }

    /**
     * Is $value something that we should treat as an image?
     *
     * Instance of
     * Image, or 2D arrays are images; 1D arrays or single values are
     * constants.
     *
     * @param mixed $value The value to test.
     *
     * @return bool true if this is like an image.
     */
    private static function _isImageish($value): bool
    {
        return self::_is2D($value) || $value instanceof Image;
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
    private static function _imageize(Image $match_image, $value): Image
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
     * swap instances of the Image for the plain resource.
     *
     * @param array $result Unwrap this.
     *
     * @return array $result unwrapped, ready for vips.
     */
    private static function _unwrap(array $result): array
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
    private static function _isImage($value): bool
    {
        return is_resource($value) &&
        get_resource_type($value) == "GObject";
    }

    /**
     * Wrap up the result of a vips_ call ready to return it to PHP. We do
     * two things:
     *
     * - If the array is a singleton, we strip it off. For example, many
     *   operations return a single result and there's no sense handling
     *   this as an array of values, so we transform ["out" => x] -> x.
     *
     * - Any VipsImage resources are rewrapped as instances of Image.
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
     * Create a new Image from a file on disc.
     *
     * @param string $filename The file to open.
     * @param array  $options  Any options to pass on to the load operation.
     *
     * @return Image A new Image.
     */
    public static function newFromFile(string $filename, array $options = []): Image
    {
        $options = self::_unwrap($options);
        $result = vips_image_new_from_file($filename, $options);
        return self::_wrap($result);
    }

    /**
     * Create a new Image from a compressed image held as a string.
     *
     * @param string $buffer        The formatted image to open.
     * @param string $option_string Any text-style options to pass to the
     *     selected loader.
     * @param array  $options       Any options to pass on to the load operation.
     *
     * @return Image A new Image.
     */
    public static function newFromBuffer(
        string $buffer,
        string $option_string = "",
        array $options = []
    ): Image {


        $options = self::_unwrap($options);
        $result = vips_image_new_from_buffer($buffer, $option_string, $options);
        return self::_wrap($result);
    }

    /**
     * Create a new Image from a php array.
     *
     * 2D arrays become 2D images. 1D arrays become 2D images with height 1.
     *
     * @param array $array  The array to make the image from.
     * @param float $scale  The "scale" metadata item. Useful for integer
     *     convolution masks.
     * @param float $offset The "offset" metadata item. Useful for integer
     *     convolution masks.
     *
     * @return Image A new Image.
     */
    public static function newFromArray(
        array $array,
        float $scale = 1.0,
        float $offset = 0.0
    ): Image {
        $result = vips_image_new_from_array($array, $scale, $offset);
        return self::_wrap($result);
    }

    /**
     * Write an image to a file.
     *
     * @param string $filename The file to write the image to.
     * @param array  $options  Any options to pass on to the selected save operation.
     *
     * @return bool true for success and false for failure.
     */
    public function writeToFile(string $filename, array $options = []): bool
    {
        $options = self::_unwrap($options);
        $result = vips_image_write_to_file($this->_image, $filename, $options);
        return self::_wrap($result);
    }

    /**
     * Write an image to a formatted string.
     *
     * @param string $suffix  The file type suffix, eg. ".jpg".
     * @param array  $options Any options to pass on to the selected save operation.
     *
     * @return string The formatted image.
     */
    public function writeToBuffer(string $suffix, array $options = []): string
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
    public function __get(string $name)
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
    public function __set(string $name, $value)
    {
        vips_image_set($this->_image, $name, $value);
    }

    /**
     * Get any property from the underlying image.
     *
     * This is handy for fields whose name
     * does not match PHP's variable naming conventions, like `"exif-data"`.
     *
     * @param string $name The property name.
     *
     * @return mixed
     */
    public function get(string $name)
    {
        $result = vips_image_get($this->_image, $name);
        return self::_wrap($result);
    }

    /**
     * Set any property on the underlying image.
     *
     * This is handy for fields whose name
     * does not match PHP's variable naming conventions, like `"exif-data"`.
     *
     * @param string $name  The property name.
     * @param mixed  $value The value to set for this property.
     *
     * @return void
     */
    public function set(string $name, $value)
    {
        vips_image_set($this->_image, $name, $value);
    }

    /**
     * Call any vips operation. The final element of $arguments can be 
     * (but doesn't have to be) an array of options to pass to the operation.
     *
     * We can't have a separate arg for the options since this will be run from
     * __call(), which cannot know which args are required and which are
     * optional. See call() below for a version with the options broken out. 
     *
     * @param string     $name      The operation name.
     * @param Image|null $instance  The instance this operation is being invoked
     *      from.
     * @param array      $arguments An array of arguments to pass to the 
     *      operation.
     *
     * @return mixed The result(s) of the operation.
     */
    public static function callBase(
        string $name,
        $instance,
        array $arguments
    ) {
        /*
        echo "call: ", $name, "\n";
        echo "instance = ";
        var_dump($instance);
        echo "arguments = ";
        var_dump($arguments);
         */

        $arguments = array_merge([$name, $instance], $arguments);

        /*
        echo "after arg composition, arguments = ";
        var_dump($arguments);
         */

        $arguments = self::_unwrap($arguments);
        $result = call_user_func_array("vips_call", $arguments);
        return self::_wrap($result);
    }

    /**
     * Call any vips operation, with an explicit set of options. This is more
     * convenient than callBase() if you have a set of known options. 
     *
     * @param string     $name      The operation name.
     * @param Image|null $instance  The instance this operation is being invoked
     *      from.
     * @param array      $arguments An array of arguments to pass to the 
     *      operation.
     * @param array      $options   An array of optional arguments to pass to 
     *      the operation.
     *
     * @return mixed The result(s) of the operation.
     */
    public static function call(
        string $name,
        $instance,
        array $arguments,
        array $options = []
    ) {
        /*
        echo "call: ", $name, "\n";
        echo "instance = ";
        var_dump($instance);
        echo "arguments = ";
        var_dump($arguments);
        echo "options = ";
        var_dump($options);
         */

        return self::callBase(
            $name, $instance, array_merge($arguments, [$options])
        );
    }

    /**
     * Handy for things like self::more. Call a 2-ary vips operator like
     * "more", but if the arg is not an image (ie. it's a constant), call
     * "more_const" instead.
     *
     * @param mixed  $other   The right-hand argument.
     * @param string $base    The base part of the operation name.
     * @param string $op      The action to invoke.
     * @param array  $options An array of options to pass to the operation.
     *
     * @return mixed The operation result.
     */
    private function _callEnum(
        $other,
        string $base,
        string $op,
        array $options = []
    ) {

        if (self::_isImageish($other)) {
            return self::call($base, $this, [$other, $op], $options);
        } else {
            return self::call($base . "_const", $this, [$op, $other], $options);
        }
    }

    /**
     * Call any vips operation as an instance method.
     *
     * @param string $name      The thing we call.
     * @param array  $arguments The arguments to the thing.
     *
     * @return mixed The result.
     */
    public function __call(string $name, array $arguments)
    {
        return self::callBase($name, $this, $arguments);
    }

    /**
     * Call any vips operation as a class method.
     *
     * @param string $name      The thing we call.
     * @param array  $arguments The arguments to the thing.
     *
     * @return mixed The result.
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return self::callBase($name, null, $arguments);
    }

    /**
     * Our ArrayAccess interface ... we allow [] to get band.
     *
     * @param mixed $offset The index to fetch.
     *
     * @return bool true if the index exists.
     */
    public function offsetExists($offset): bool
    {
        return $offset >= 0 && $offset <= $this->bands - 1;
    }

    /**
     * Our ArrayAccess interface ... we allow [] to get band.
     *
     * @param mixed $offset The index to fetch.
     *
     * @return Image the extracted band.
     */
    public function offsetGet($offset): Image
    {
        return self::offsetExists($offset) ? self::extract_band($offset) : null;
    }

    /**
     * Our ArrayAccess interface ... we allow [] to get band.
     *
     * @param mixed $offset The index to set.
     * @param Image $value  The band to insert
     *
     * @return Image the expanded image.
     */
    public function offsetSet($offset, $value): Image
    {
        throw new \BadMethodCallException("Image::offsetSet: not implemented");
    }

    /**
     * Our ArrayAccess interface ... we allow [] to get band.
     *
     * @param mixed $offset The index to remove.
     *
     * @return Image the reduced image.
     */
    public function offsetUnset($offset): Image
    {
        throw new \BadMethodCallException("Image::offsetUnset: not implemented");
    }

    /**
     * Add $other to this image.
     *
     * @param mixed $other   The thing to add to this image.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function add($other, array $options = []): Image
    {
        if (self::_isImageish($other)) {
            return self::call("add", $this, [$other], $options);
        } else {
            return self::linear(1, $other, $options);
        }
    }

    /**
     * Subtract $other from this image.
     *
     * @param mixed $other   The thing to subtract from this image.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function subtract($other, array $options = []): Image
    {
        if (self::_isImageish($other)) {
            return self::call("subtract", $this, [$other], $options);
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
     * @param mixed $other   The thing to multiply this image by.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function multiply($other, array $options = []): Image
    {
        if (self::_isImageish($other)) {
            return self::call("multiply", $this, [$other], $options);
        } else {
            return self::linear($other, 0, $options);
        }
    }

    /**
     * Divide this image by $other.
     *
     * @param mixed $other   The thing to divide this image by.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function divide($other, array $options = []): Image
    {
        if (self::_isImageish($other)) {
            return self::call("divide", $this, [$other], $options);
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
     * @param mixed $other   The thing to take the remainder with.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function remainder($other, array $options = []): Image
    {
        if (self::_isImageish($other)) {
            return self::call("remainder", $this, [$other], $options);
        } else {
            return self::call("remainder_const", $this, [$other], $options);
        }
    }

    /**
     * Find $this to the power of $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function pow($other, array $options = []): Image
    {
        return self::_callEnum($other, "math2", "pow", $options);
    }

    /**
     * Find $other to the power of $this.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function wop($other, array $options = []): Image
    {
        return self::_callEnum($other, "math2", "wop", $options);
    }

    /**
     * Shift $this left by $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function lshift($other, array $options = []): Image
    {
        return self::_callEnum($other, "boolean", "lshift", $options);
    }

    /**
     * Shift $this right by $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function rshift($other, array $options = []): Image
    {
        return self::_callEnum($other, "boolean", "rshift", $options);
    }

    /**
     * Bitwise AND of $this and $other. This has to be called ->andimage()
     * rather than ->and() to avoid confusion in phpdoc.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function andimage($other, array $options = []): Image
    {
        return self::_callEnum($other, "boolean", "and", $options);
    }

    /**
     * Bitwise OR of $this and $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function orimage($other, array $options = []): Image
    {
        return self::_callEnum($other, "boolean", "or", $options);
    }

    /**
     * Bitwise EOR of $this and $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function eorimage($other, array $options = []): Image
    {
        return self::_callEnum($other, "boolean", "eor", $options);
    }

    /**
     * 255 where $this is more than $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function more($other, array $options = []): Image
    {
        return self::_callEnum($other, "relational", "more", $options);
    }

    /**
     * 255 where $this is more than or equal to $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function moreEq($other, array $options = []): Image
    {
        return self::_callEnum($other, "relational", "moreeq", $options);
    }

    /**
     * 255 where $this is less than $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function less($other, array $options = []): Image
    {
        return self::_callEnum($other, "relational", "less", $options);
    }

    /**
     * 255 where $this is less than or equal to $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function lessEq($other, array $options = []): Image
    {
        return self::_callEnum($other, "relational", "lesseq", $options);
    }

    /**
     * 255 where $this is equal to $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function equal($other, array $options = []): Image
    {
        return self::_callEnum($other, "relational", "equal", $options);
    }

    /**
     * 255 where $this is not equal to $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function notEq($other, array $options = []): Image
    {
        return self::_callEnum($other, "relational", "noteq", $options);
    }

    /**
     * Join $this and $other bandwise.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function bandjoin($other, array $options = []): Image
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
            return self::call("bandjoin_const", $this, [$other], $options);
        } else {
            return self::call(
                "bandjoin", null, [array_merge([$this], $other)], $options
            );
        }
    }

    /**
     * Split $this into an array of single-band images.
     *
     * @param array $options An array of options to pass to the operation.
     *
     * @return array An array of images.
     */
    public function bandsplit(array $options = []): array
    {
        $result = [];

        for ($i = 0; $i < $this->bands; $i++) {
            $result[] = $this->extract_band($i, $options);
        }

        return $result;
    }

    /**
     * For each band element, sort the array of input images and pick the
     * median. Use the index option to pick something else.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function bandrank($other, array $options = []): Image
    {
        /* bandrank will appear as a static class member, as 
         * Image::bandrank([a, b, c]), but it's better as an instance
         * method.
         * 
         * We need to define this by hand.
         */

        /* Allow a single unarrayed value as well.
         */
        if (!is_array($other)) {
            $other = [$other];
        }

        return self::call("bandrank", $this, $other, $options);
    }

    /**
     * Position of max is awkward with plain self::max.
     *
     * @return array (float, int, int) The value and position of the maximum.
     */
    public function maxpos(): array
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
     * @return array (float, int, int) The value and position of the minimum.
     */
    public function minpos(): array
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
     * @param mixed $then    The true side of the operator
     * @param mixed $else    The false side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @return Image A new image.
     */
    public function ifthenelse($then, $else, array $options = []): Image
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

        return self::call("ifthenelse", $this, [$then, $else], $options);
    }

    /**
     * Return the largest integral value not greater than the argument.
     *
     * @return Image A new image.
     */
    public function floor(): Image
    {
        return $this->round("floor");
    }

    /**
     * Return the smallest integral value not less than the argument.
     *
     * @return Image A new image.
     */
    public function ceil(): Image
    {
        return $this->round("ceil");
    }

    /**
     * Return the nearest integral value.
     *
     * @return Image A new image.
     */
    public function rint(): Image
    {
        return $this->round("rint");
    }

    /**
     * AND image bands together.
     *
     * @return Image A new image.
     */
    public function bandand(): Image
    {
        return $this->bandbool("and");
    }

    /**
     * OR image bands together.
     *
     * @return Image A new image.
     */
    public function bandor(): Image
    {
        return $this->bandbool("or");
    }

    /**
     * EOR image bands together.
     *
     * @return Image A new image.
     */
    public function bandeor(): Image
    {
        return $this->bandbool("eor");
    }

    /**
     * Return the real part of a complex image.
     *
     * @return Image A new image.
     */
    public function real(): Image
    {
        return $this->complexget("real");
    }

    /**
     * Return the imaginary part of a complex image.
     *
     * @return Image A new image.
     */
    public function imag(): Image
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
     * @return Image A new image.
     */
    public function polar(): Image
    {
        return $this->complex("polar");
    }

    /**
     * Return an image converted to rectangular coordinates.
     *
     * @return Image A new image.
     */
    public function rect(): Image
    {
        return $this->complex("rect");
    }

    /**
     * Return the complex conjugate of an image.
     *
     * @return Image A new image.
     */
    public function conj(): Image
    {
        return $this->complex("conj");
    }

    /**
     * Return the sine of an image in degrees.
     *
     * @return Image A new image.
     */
    public function sin(): Image
    {
        return $this->math("sin");
    }

    /**
     * Return the cosine of an image in degrees.
     *
     * @return Image A new image.
     */
    public function cos(): Image
    {
        return $this->math("cos");
    }

    /**
     * Return the tangent of an image in degrees.
     *
     * @return Image A new image.
     */
    public function tan(): Image
    {
        return $this->math("tan");
    }

    /**
     * Return the inverse sine of an image in degrees.
     *
     * @return Image A new image.
     */
    public function asin(): Image
    {
        return $this->math("asin");
    }

    /**
     * Return the inverse cosine of an image in degrees.
     *
     * @return Image A new image.
     */
    public function acos(): Image
    {
        return $this->math("acos");
    }

    /**
     * Return the inverse tangent of an image in degrees.
     *
     * @return Image A new image.
     */
    public function atan(): Image
    {
        return $this->math("atan");
    }

    /**
     * Return the natural log of an image.
     *
     * @return Image A new image.
     */
    public function log(): Image
    {
        return $this->math("log");
    }

    /**
     * Return the log base 10 of an image.
     *
     * @return Image A new image.
     */
    public function log10(): Image
    {
        return $this->math("log10");
    }

    /**
     * Return e ** pixel.
     *
     * @return Image A new image.
     */
    public function exp(): Image
    {
        return $this->math("exp");
    }

    /**
     * Return 10 ** pixel.
     *
     * @return Image A new image.
     */
    public function exp10(): Image
    {
        return $this->math("exp10");
    }

    /**
     * Erode with a structuring element.
     *
     * @param mixed $mask Erode with this structuring element.
     *
     * @return Image A new image.
     */
    public function erode($mask): Image
    {
        return $this->morph($mask, "erode");
    }

    /**
     * Dilate with a structuring element.
     *
     * @param mixed $mask Dilate with this structuring element.
     *
     * @return Image A new image.
     */
    public function dilate($mask): Image
    {
        return $this->morph($mask, "dilate");
    }

    /**
     * $size x $size median filter.
     *
     * @param int $size Size of median filter.
     *
     * @return Image A new image.
     */
    public function median(int $size): Image
    {
        return $this->rank($size, $size, ($size * $size) / 2);
    }

    /**
     * Flip horizontally.
     *
     * @return Image A new image.
     */
    public function fliphor(): Image
    {
        return $this->flip("horizontal");
    }

    /**
     * Flip vertically.
     *
     * @return Image A new image.
     */
    public function flipver(): Image
    {
        return $this->flip("vertical");
    }

    /**
     * Rotate 90 degrees clockwise.
     *
     * @return Image A new image.
     */
    public function rot90(): Image
    {
        return $this->rot("d90");
    }

    /**
     * Rotate 180 degrees.
     *
     * @return Image A new image.
     */
    public function rot180(): Image
    {
        return $this->rot("d180");
    }

    /**
     * Rotate 270 degrees clockwise.
     *
     * @return Image A new image.
     */
    public function rot270(): Image
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
