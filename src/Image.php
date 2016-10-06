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

require 'auto_docs.php';

/**
 * This class represents a Vips image object.
 *
 * This module provides a binding for the [vips image processing 
 * library](http://www.vips.ecs.soton.ac.uk).
 *
 * It needs libvips 8.0 or later to be installed, and it needs the binary 
 * [`vips` extension](https://github.com/jcupitt/php-vips-ext) to be added to
 * your PHP.
 *
 * # Example
 *
 * ```php
 * <?php
 * use Jcupitt\Vips;
 * $im = Vips\Image::newFromFile($argv[1], ["access" => "sequential"]);
 * $im = $im->crop(100, 100, $im->width - 200, $im->height - 200);
 * $im = $im->reduce(1.0 / 0.9, 1.0 / 0.9, ["kernel" => "linear"]);
 * $mask = Vips\Image::newFromArray(
 *      [[-1,  -1, -1], 
 *       [-1,  16, -1], 
 *       [-1,  -1, -1]], 8);
 * $im = $im->conv($mask);
 * $im->writeToFile($argv[2]);
 * ?>
 * ```
 *
 * You'll need this in your `composer.json`:
 * 
 * ```
 *     "require": {
 *         "jcupitt/vips" : "@dev"
 *     }
 * ```
 * 
 * And run with:
 * 
 * ```
 * $ composer install
 * $ ./try1.php ~/pics/k2.jpg x.tif
 * ```
 * 
 * This example loads a file, crops 100 pixels from every edge, reduces by 10%
 * using a bilinear interpolator (the default is lanczos3), 
 * sharpens the image, and saves it back to disc again. 
 *
 * Reading this example line by line, we have:
 *
 * ```php
 * $im = Vips\Image::newFromFile($argv[1], ["access" => "sequential"]);
 * ```
 *
 * `Image::newFromFile` can load any image file supported by vips. Almost all
 * operations can be given an array of options as a final argument.
 *
 * In this
 * example, we will be accessing pixels top-to-bottom as we sweep through the
 * image reading and writing, so `sequential` access mode is best for us. The
 * default mode is `random`, this allows for full random access to image pixels,
 * but is slower and needs more memory. 
 *
 * You can also load formatted images from 
 * strings or create images from PHP arrays.
 *
 * The next line:
 *
 * ```php
 * $im = $im->crop(100, 100, $im->width - 200, $im->height - 200);
 * ```
 *
 * Crops 100 pixels from every edge. You can access any vips image property 
 * directly as a PHP property. If the vips property name does not
 * conform to PHP naming conventions, you can use something like 
 * `$image->get("ipct-data")`.
 *
 * Next we have:
 *
 * ```php
 * $mask = Vips\Image::newFromArray(
 *      [[-1,  -1, -1], 
 *       [-1,  16, -1], 
 *       [-1,  -1, -1]], 8);
 * $im = $im->conv($mask);
 * ```
 *
 * `Image::new_from_array` creates an image from an array constant. The 8 at
 * the end sets the scale: the amount to divide the image by after 
 * integer convolution. See the libvips API docs for `vips_conv()` (the operation
 * invoked by `Image::conv`) for details on the convolution operator. See
 * **Getting more help** below.
 *
 * Finally:
 *
 * ```php
 * $im->writeToFile($argv[2]);
 * ```
 *
 * `Image::writeToFile` writes an image back to the filesystem. It can 
 * write any format supported by vips: the file type is set from the filename 
 * suffix. You can also write formatted images to strings.
 *
 * # Getting more help
 *
 * This binding lets you call the complete C API almost directly. You should 
 * [consult the C docs](
 * http://www.vips.ecs.soton.ac.uk/supported/current/doc/html/libvips/index.html)
 * for full details on the operations that are available and
 * the arguments they take. There's a handy [function list](
 * http://www.vips.ecs.soton.ac.uk/supported/current/doc/html/libvips/func-list.html)
 * which summarises the operations in the library. You can use the `vips`
 * command-line interface to get help as well, for example:
 *
 * ```
 * $ vips embed
 * embed an image in a larger image
 * usage:
 *    embed in out x y width height
 * where:
 *    in           - Input image, input VipsImage
 *    out          - Output image, output VipsImage
 *    x            - Left edge of input in output, input gint
 *                     default: 0
 *                     min: -1000000000, max: 1000000000
 *    y            - Top edge of input in output, input gint
 *                     default: 0
 *                     min: -1000000000, max: 1000000000
 *    width        - Image width in pixels, input gint
 *                     default: 1
 *                     min: 1, max: 1000000000
 *    height       - Image height in pixels, input gint
 *                     default: 1
 *                     min: 1, max: 1000000000
 * optional arguments:
 *    extend       - How to generate the extra pixels, input VipsExtend
 *                     default: black
 *                     allowed: black, copy, repeat, mirror, white, background
 *    background   - Colour for background pixels, input VipsArrayDouble
 * operation flags: sequential-unbuffered 
 * ```
 *
 * You can call this from PHP as:
 *
 * ```php
 * $out = $in->embed($x, $y, $width, $height, 
 *     ["extend" => "copy", "background" => [1, 2, 3]]);
 * ```
 *
 * `"background"` can also be a simple constant, such as `12`, see below.
 *
 * The `vipsheader` command-line program is an easy way to see all the
 * properties of an image. For example:
 *
 * ```
 * $ vipsheader -a ~/pics/k2.jpg
 * /home/john/pics/k2.jpg: 1450x2048 uchar, 3 bands, srgb, jpegload
 * width: 1450
 * height: 2048
 * bands: 3
 * format: 0 - uchar
 * coding: 0 - none
 * interpretation: 22 - srgb
 * xoffset: 0
 * yoffset: 0
 * xres: 2.834646
 * yres: 2.834646
 * filename: "/home/john/pics/k2.jpg"
 * vips-loader: jpegload
 * jpeg-multiscan: 0
 * ipct-data: VIPS_TYPE_BLOB, data = 0x20f0010, length = 332
 * ```
 *
 * You can access any of these fields as PHP properties of the `Image` class.
 * Use `$image->get("ipct-data")` for property names which are not valid under
 * PHP syntax.
 *
 * # How it works
 *
 * The binary 
 * [`vips` extension](https://github.com/jcupitt/php-vips-ext) adds a few extra
 * functions to PHP to let you call anything in the libvips library. The API 
 * it provides is simple, but horrible. 
 *
 * This module is pure PHP and builds on the binary extension to provide a
 * convenient interface for programmers. It uses the PHP magic methods
 * `__call()`, `__callStatic()`, `__get()` and `__set()` to make vips operators
 * appear as methods on the `Image` class, and vips properties as PHP
 * properties.
 *
 * The API you end up with is a object-oriented version of the [VIPS C 
 * API](http://www.vips.ecs.soton.ac.uk/supported/current/doc/html/libvips/). 
 * Full documentation
 * on the operations and what they do is there, you can use it directly. This
 * document explains the extra features of the PHP API and lists the available 
 * operations very briefly. 
 *
 * # Automatic wrapping
 *
 * This binding has a `__call()` method and uses
 * it to look up vips operations. For example, the libvips operation `embed`, 
 * which appears in C as `vips_embed()`, appears in PHP as `Image::embed`. 
 *
 * The operation's list of required arguments is searched and the first input 
 * image is set to the value of `self`. Operations which do not take an input 
 * image, such as `Image::black`, appear as static methods. The remainder of
 * the arguments you supply in the function call are used to set the other
 * required input arguments. If the final supplied argument is an array, it is 
 * used to set any optional input arguments. The result is the required output 
 * argument if there is only one result, or an array of values if the operation
 * produces several results.
 *
 * For example, `Image::min`, the vips operation that searches an image for 
 * the minimum value, has a large number of optional arguments. You can use it to
 * find the minimum value like this:
 *
 * ```php
 * $min_value = $image->min();
 * ```
 *
 * You can ask it to return the position of the minimum with `x` and `y`.
 *   
 * ```php
 * $result = $image->min(["x" => true, "y" => true]);
 * $min_value = $result["out"];
 * $x_pos = $result["x"];
 * $y_pos = $result["y"];
 * ```
 *
 * Now `x_pos` and `y_pos` will have the coordinates of the minimum value. 
 * There's actually a convenience function for this, `Image::minpos`, see
 * below.
 *
 * You can also ask for the top *n* minimum, for example:
 *
 * ```php
 * $result = $image->min(["size" => 10, "x_array" => true, "y_array" => true]);
 * $x_pos = $result["x_array"];
 * $y_pos = $result["y_array"];
 * ```
 *
 * Now `x_pos` and `y_pos` will be 10-element arrays. 
 *
 * Because operations are member functions and return the result image, you can
 * chain them. For example, you can write:
 *
 * ```php
 * $result_image = $image->real()->cos();
 * ```
 *
 * to calculate the cosine of the real part of a complex image. 
 *
 * libvips types are also automatically wrapped and unwrapped. The binding 
 * looks at the type 
 * of argument required by the operation and converts the value you supply, 
 * when it can. For example, `Image::linear` takes a `VipsArrayDouble` as 
 * an argument 
 * for the set of constants to use for multiplication. You can supply this 
 * value as an integer, a float, or some kind of compound object and it 
 * will be converted for you. You can write:
 *
 * ```php
 * $result = $image->linear(1, 3);
 * $result = $image->linear(12.4, 13.9);
 * $result = $image->linear([1, 2, 3], [4, 5, 6]);
 * $result = $image->linear(1, [4, 5, 6]);
 * ```
 *
 * And so on. You can also use `Image::add()` and friends, see below.
 *
 * It does a couple of more ambitious conversions. It will automatically convert
 * to and from the various vips types, like `VipsBlob` and `VipsArrayImage`. For
 * example, you can read the ICC profile out of an image like this: 
 *
 * ```php
 * $profile = $image->get("icc-profile-data");
 * ```
 *
 * and `$profile` will be a PHP string.
 *
 * If an operation takes several input images, you can use a constant for all but
 * one of them and the wrapper will expand the constant to an image for you. For
 * example, `Image::ifthenelse()` uses a condition image to pick pixels 
 * between a then and an else image:
 *
 * ```php
 * $result = $condition->ifthenelse($then_image, $else_image);
 * ```
 *
 * You can use a constant instead of either the then or the else parts and it
 * will be expanded to an image for you. If you use a constant for both then and
 * else, it will be expanded to match the condition image. For example:
 *
 * ```php
 * $result = $condition->ifthenelse([0, 255, 0], [255, 0, 0]);
 * ```
 *
 * Will make an image where true pixels are green and false pixels are red.
 *
 * This is useful for `Image::bandjoin`, the thing to join two or more 
 * images up bandwise. You can write:
 *
 * ```php
 * $rgba = $rgb->bandjoin(255);
 * ```
 *
 * to append a constant 255 band to an image, perhaps to add an alpha channel. Of
 * course you can also write:
 *
 * ```ruby
 * $result = $image->bandjoin($image2);
 * $result = $image->bandjoin([image2, image3]);
 * $result = Image::bandjoin([image1, image2, image3]);
 * $result = $image->bandjoin([image2, 255]);
 * ```
 *
 * and so on. 
 * 
 * # Exceptions
 *
 * The wrapper spots errors from vips operations and raises `\Exception`.
 * You can catch it in the usual way. 
 *
 * # Draw operations
 *
 * Paint operations like `Image::draw_circle` and `Image::draw_line`
 * modify their input image. This
 * makes them hard to use with the rest of libvips: you need to be very careful
 * about the order in which operations execute or you can get nasty crashes.
 *
 * The wrapper spots operations of this type and makes a private copy of the
 * image in memory before calling the operation. This stops crashes, but it does
 * make it inefficient. If you draw 100 lines on an image, for example, you'll
 * copy the image 100 times. The wrapper does make sure that memory is recycled
 * where possible, so you won't have 100 copies in memory. 
 *
 * If you want to avoid the copies, you'll need to call drawing operations
 * yourself.
 *
 * # Expansions
 *
 * Some vips operators take an enum to select an action, for example 
 * `Image::math` can be used to calculate sine of every pixel like this:
 *
 * ```php
 * $result = $image->math("sin");
 * ```
 *
 * This is annoying, so the wrapper expands all these enums into separate members
 * named after the enum. So you can write:
 *
 * ```php
 * $result = $image->sin();
 * ```
 *
 * # Convenience functions
 *
 * The wrapper defines a few extra useful utility functions: 
 * `Image::get`, `Image::set`, `Image::bandsplit`, 
 * `Image::maxpos`, `Image::minpos`, 
 * `Image::median`. See below.
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
     * Set true when we are logging actions.
     *
     * @internal 
     */
    static private $_enable_logging = false;

    /**
     * Turn logging on and off. This will produce a huge amount of output, but
     * is handy for debugging.
     *
     * @param bool $logging true to turn logging on.
     *
     * @return void
     */
    public static function setLogging($logging)
    {
        self::$_enable_logging = $logging;
    }

    /**
     * The resource for the underlying VipsImage.
     *
     * @internal 
     */
    private $_image;

    /**
     * Wrap a Image around an underlying vips resource.
     *
     * Don't call this yourself, users should stick to (for example)
     * Image::newFromFile().
     *
     * @param resource $image The underlying vips image resource that this
     *      class should wrap.
     *
     * @internal 
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
     *
     * @internal 
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
     *
     * @internal 
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
     * Instance of Image, or 2D arrays are images; 1D arrays or single values 
     * are constants.
     *
     * @param mixed $value The value to test.
     *
     * @return bool true if this is like an image.
     *
     * @internal 
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
     *
     * @internal 
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
     *
     * @internal 
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
     *
     * @internal 
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
     *
     * @internal 
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
     * Check the result of a vips_ call for an error, and throw an exception
     * if we see one.
     *
     * This won't work for things like __get where a non-array return can be 
     * a valid return.
     *
     * @param mixed $result Test this.
     *
     * @return void
     *
     * @internal 
     */
    private static function _errorCheck($result)
    {
        if (!is_array($result)) {
            $message = vips_error_buffer();
            if (self::$_enable_logging) {
                echo "failure:  $message\n";
            }
            throw new \Exception($message);
        }
    }

    /**
     * Create a new Image from a file on disc.
     *
     * @param string $filename The file to open.
     * @param array  $options  Any options to pass on to the load operation.
     *
     * @return Image A new Image.
     */
    public static function newFromFile(
        string $filename, array $options = []
    ): Image {
        $options = self::_unwrap($options);
        $result = vips_image_new_from_file($filename, $options);
        self::_errorCheck($result);
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
        self::_errorCheck($result);
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
        if ($result == -1) {
            $message = vips_error_buffer();
            if (self::$_enable_logging) {
                echo "failure:  $message\n";
            }
            throw new \Exception($message);
        }
        return self::_wrap($result);
    }

    /**
     * Write an image to a file.
     *
     * @param string $filename The file to write the image to.
     * @param array  $options  Any options to pass on to the selected save 
     *     operation.
     *
     * @return void
     */
    public function writeToFile(string $filename, array $options = [])
    {
        $options = self::_unwrap($options);
        $result = vips_image_write_to_file($this->_image, $filename, $options);
        if ($result == -1) {
            $message = vips_error_buffer();
            if (self::$_enable_logging) {
                echo "failure:  $message\n";
            }
            throw new \Exception($message);
        }
    }

    /**
     * Write an image to a formatted string.
     *
     * @param string $suffix  The file type suffix, eg. ".jpg".
     * @param array  $options Any options to pass on to the selected save 
     *     operation.
     *
     * @return string The formatted image.
     */
    public function writeToBuffer(string $suffix, array $options = []): string
    {
        $options = self::_unwrap($options);
        $result = vips_image_write_to_buffer($this->_image, $suffix, $options);
        if ($result == -1) {
            $message = vips_error_buffer();
            if (self::$_enable_logging) {
                echo "failure:  $message\n";
            }
            throw new \Exception($message);
        }
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
        self::_errorCheck($result);
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
        self::_errorCheck($result);
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
        if (self::$_enable_logging) {
            echo "callBase: ", $name, "\n";
            echo "instance = ";
            var_dump($instance);
            echo "arguments = ";
            var_dump($arguments);
        }

        $arguments = array_merge([$name, $instance], $arguments);

        /*
        echo "after arg composition, arguments = ";
        var_dump($arguments);
         */

        $arguments = self::_unwrap($arguments);
        $result = call_user_func_array("vips_call", $arguments);
        self::_errorCheck($result);
        $result =  self::_wrap($result);

        if (self::$_enable_logging) {
            echo "result = ";
            var_dump($result);
        }

        return $result;
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
     *
     * @internal 
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
