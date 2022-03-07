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
 * @link      https://github.com/jcupitt/php-vips
 */

namespace Jcupitt\Vips;

/**
 * This class represents a Vips image object.
 *
 * This module provides a binding for the [vips image processing
 * library](https://libvips.org) version 8.7 and later, and required PHP 7.4
 * and later.
 *
 * # Example
 *
 * ```php
 * <?php
 * use Jcupitt\Vips;
 * $im = Vips\Image::newFromFile($argv[1], ['access' => 'sequential']);
 * $im = $im->crop(100, 100, $im->width - 200, $im->height - 200);
 * $im = $im->reduce(1.0 / 0.9, 1.0 / 0.9, ['kernel' => 'linear']);
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
 * $im = Vips\Image::newFromFile($argv[1], ['access' => 'sequential']);
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
 * `$image->get('ipct-data')`.
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
 * `Image::newFromArray` creates an image from an array constant. The 8 at
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
 * suffix. You can write formatted images to strings, and pixel values to
 * arrays.
 *
 * # Getting more help
 *
 * This binding lets you call the complete C API almost directly. You should
 * [consult the C docs](https://jcupitt.github.io/libvips/API/current)
 * for full details on the operations that are available and
 * the arguments they take. There's a handy [function
 * list](https://jcupitt.github.io/libvips/API/current/func-list.html)
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
 *     ['extend' => 'copy', 'background' => [1, 2, 3]]);
 * ```
 *
 * `'background'` can also be a simple constant, such as `12`, see below.
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
 * Use `$image->get('ipct-data')` for property names which are not valid under
 * PHP syntax.
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
 * the minimum value, has a large number of optional arguments. You can use it
 * to find the minimum value like this:
 *
 * ```php
 * $min_value = $image->min();
 * ```
 *
 * You can ask it to return the position of the minimum with `x` and `y`.
 *
 * ```php
 * $result = $image->min(['x' => true, 'y' => true]);
 * $min_value = $result['out'];
 * $x_pos = $result['x'];
 * $y_pos = $result['y'];
 * ```
 *
 * Now `x_pos` and `y_pos` will have the coordinates of the minimum value.
 * There's actually a convenience function for this, `Image::minpos`, see
 * below.
 *
 * You can also ask for the top *n* minimum, for example:
 *
 * ```php
 * $result = $image->min(['size' => 10, 'x_array' => true, 'y_array' => true]);
 * $x_pos = $result['x_array'];
 * $y_pos = $result['y_array'];
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
 * $profile = $image->get('icc-profile-data');
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
 * ```php
 * $result = $image->bandjoin($image2);
 * $result = $image->bandjoin([image2, image3]);
 * $result = Image::bandjoin([image1, image2, image3]);
 * $result = $image->bandjoin([image2, 255]);
 * ```
 *
 * and so on.
 *
 * # Array access
 *
 * Images can be treated as arrays of bands. You can write:
 *
 * ```php
 * $result = $image[1];
 * ```
 *
 * to get band 1 from an image (green, in an RGB image).
 *
 * You can assign to bands as well. You can write:
 *
 * ```php
 * $image[1] = $other_image;
 * ```
 *
 * And band 1 will be replaced by all the bands in `$other_image` using
 * `bandjoin`. Use no offset to mean append, use -1 to mean prepend:
 *
 * ```php
 * $image[] = $other_image; // append bands from other
 * $image[-1] = $other_image; // prepend bands from other
 * ```
 *
 * You can use number and array constants as well, for example:
 *
 * ```php
 * $image[] = 255; // append a constant 255
 * $image[1] = [1, 2, 3]; // swap band 1 for three constant bands
 * ```
 *
 * Finally, you can delete bands with `unset`:
 *
 * ```php
 * unset($image[1]); // remove band 1
 * ```
 *
 * # Exceptions
 *
 * The wrapper spots errors from vips operations and throws
 * `Vips\Exception`. You can catch it in the usual way.
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
 * # Thumbnailing
 *
 * The thumbnailing functionality is implemented by `Vips\Image::thumbnail` and
 * `Vips\Image::thumbnail_buffer` (which thumbnails an image held as a string).
 *
 * You could write:
 *
 * ```php
 * $filename = 'image.jpg';
 * $image = Vips\Image::thumbnail($filename, 200, ['height' => 200]);
 * $image->writeToFile('my-thumbnail.jpg');
 * ```
 *
 * # Resample
 *
 * There are three types of operation in this section.
 *
 * First, `->affine()` applies an affine transform to an image.
 * This is any sort of 2D transform which preserves straight lines;
 * so any combination of stretch, sheer, rotate and translate.
 * You supply an interpolator for it to use to generate pixels
 * (@see Image::newInterpolator()). It will not produce good results for
 * very large shrinks: you'll see aliasing.
 *
 * `->reduce()` is like `->affine()`, but it can only shrink images,
 * it can't enlarge, rotate, or skew.
 * It's very fast and uses an adaptive kernel (@see Kernel for possible values)
 * for interpolation. It will be slow for very large shrink factors.
 *
 * `->shrink()` is a fast block shrinker. It can quickly reduce images by large
 * integer factors. It will give poor results for small size reductions:
 * again, you'll see aliasing.
 *
 * Next, `->resize()` specialises in the common task of image reduce and enlarge.
 * It strings together combinations of `->shrink()`, `->reduce()`, `->affine()`
 * and others to implement a general, high-quality image resizer.
 *
 * Finally, `->mapim()` can apply arbitrary 2D image transforms to an image.
 *
 * # Expansions
 *
 * Some vips operators take an enum to select an action, for example
 * `Image::math` can be used to calculate sine of every pixel like this:
 *
 * ```php
 * $result = $image->math('sin');
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
 * # Logging
 *
 * Use `Config::setLogger` to enable logging in the usual manner. A
 * sample logger, handy for debugging, is defined in `Vips\DebugLogger`. You
 * can enable debug logging with:
 *
 * ```php
 * Vips\Config::setLogger(new Vips\DebugLogger);
 * ```
 *
 * # Configuration
 *
 * You can use the methods in `Vips\Config` to set various global properties of
 * the library. For example, you can control the size of the libvips cache,
 * or the size of the worker threadpools that libvips uses to evaluate images.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/jcupitt/php-vips
 */
class Image extends ImageAutodoc implements \ArrayAccess
{
    /**
     * A pointer to the underlying VipsImage. This is the same as the
     * GObject, just cast to VipsImage to help FFI.
     *
     * @internal
     */
    public \FFI\CData $pointer;

    /**
     * Wrap an Image around an underlying CData pointer.
     *
     * Don't call this yourself, users should stick to (for example)
     * Image::newFromFile().
     *
     * @internal
     */
    public function __construct($pointer)
    {
        $this->pointer = Config::ffi()->
            cast(Config::ctypes("VipsImage"), $pointer);
        parent::__construct($pointer);
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
    private static function mapNumeric($value, \Closure $func)
    {
        if (is_numeric($value)) {
            $value = $func($value);
        } elseif (is_array($value)) {
            array_walk_recursive($value, function (&$value) use ($func) {
                $value = self::mapNumeric($value, $func);
            });
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
    private static function is2D($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (!is_array($value[0])) {
            return false;
        }
        $width = count($value[0]);

        foreach ($value as $row) {
            if (!is_array($row) || count($row) !== $width) {
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
    public static function isImageish($value): bool
    {
        return self::is2D($value) || $value instanceof Image;
    }

    /**
     * Turn a constant (eg. 1, '12', [1, 2, 3], [[1]]) into an image using
     * this as a guide.
     *
     * @param mixed $value       Turn this into an image.
     *
     * @throws Exception
     *
     * @return Image The image we created.
     *
     * @internal
     */
    public function imageize($value): Image
    {
        if ($value instanceof Image) {
            return $value;
        } elseif (self::is2D($value)) {
            return self::newFromArray($value);
        } else {
            return $this->newFromImage($value);
        }
    }

    /**
     * Run a function expecting a complex image. If the image is not in complex
     * format, try to make it complex by joining adjacant bands as real and
     * imaginary.
     *
     * @param \Closure $func  The function to run.
     * @param Image    $image The image to run the function on.
     *
     * @throws Exception
     *
     * @return Image A new Image.
     *
     * @internal
     */
    private static function runCmplx(\Closure $func, Image $image): Image
    {
        $original_format = $image->format;

        if ($image->format != 'complex' && $image->format != 'dpcomplex') {
            if ($image->bands % 2 != 0) {
                throw new Exception('not an even number of bands');
            }

            if ($image->format != 'float' && $image->format != 'double') {
                $image = $image->cast('float');
            }

            if ($image->format == 'double') {
                $new_format = 'dpcomplex';
            } else {
                $new_format = 'complex';
            }

            $image = $image->copy(['format' => $new_format,
                                   'bands' => $image->bands / 2]);
        }

        $image = $func($image);

        if ($original_format != 'complex' && $original_format != 'dpcomplex') {
            if ($image->format == 'dpcomplex') {
                $new_format = 'double';
            } else {
                $new_format = 'float';
            }

            $image = $image->copy(['format' => $new_format,
                                   'bands' => $image->bands * 2]);
        }

        return $image;
    }

    /**
     * Handy for things like self::more. Call a 2-ary vips operator like
     * 'more', but if the arg is not an image (ie. it's a constant), call
     * 'more_const' instead.
     *
     * @param mixed  $other   The right-hand argument.
     * @param string $base    The base part of the operation name.
     * @param string $op      The action to invoke.
     * @param array  $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return mixed The operation result.
     *
     * @internal
     */
    private function callEnum(
        $other,
        string $base,
        string $op,
        array $options = []
    ) {
        if (self::isImageish($other)) {
            return VipsOperation::call($base, $this, [$other, $op], $options);
        } else {
            return VipsOperation::call(
                $base . '_const',
                $this,
                [$op, $other],
                $options
            );
        }
    }

    /**
     * Find the name of the load operation vips will use to load a file, for
     * example "VipsForeignLoadJpegFile". You can use this to work out what
     * options to pass to newFromFile().
     *
     * @param string $filename The file to test.
     *
     * @return string|null The name of the load operation, or null.
     */
    public static function findLoad(string $filename): ?string
    {
        Utils::debugLog('findLoad', [
            'instance' => null,
            'arguments' => [$filename]
        ]);

        $result = Config::ffi()->vips_foreign_find_load($filename);

        Utils::debugLog('findLoad', ['result' => [$result]]);

        return $result;
    }

    /**
     * Create a new Image from a file on disc.
     *
     * @param string $filename The file to open.
     * @param array  $options  Any options to pass on to the load operation.
     *
     * @throws Exception
     *
     * @return Image A new Image.
     */
    public static function newFromFile(
        string $name,
        array $options = []
    ): Image {
        Utils::debugLog('newFromFile', [
            'instance' => null,
            'arguments' => [$name, $options]
        ]);

        $filename = Config::filenameGetFilename($name);
        $string_options = Config::filenameGetOptions($name);

        $loader = self::findLoad($filename);
        if ($loader == null) {
            Config::error();
        }

        if (strlen($string_options) != 0) {
            $options = array_merge([
                "string_options" => $string_options,
            ], $options);
        }

        $result = VipsOperation::call($loader, null, [$filename], $options);

        Utils::debugLog('newFromFile', ['result' => $result]);

        return $result;
    }

    /**
     * Find the name of the load operation vips will use to load a buffer, for
     * example 'VipsForeignLoadJpegBuffer'. You can use this to work out what
     * options to pass to newFromBuffer().
     *
     * @param string $buffer The formatted image to test.
     *
     * @return string|null The name of the load operation, or null.
     */
    public static function findLoadBuffer(string $buffer): ?string
    {
        Utils::debugLog('findLoadBuffer', [
            'instance' => null,
            'arguments' => [$buffer]
        ]);

        $result = Config::ffi()->
            vips_foreign_find_load_buffer($buffer, strlen($buffer));

        Utils::debugLog('findLoadBuffer', ['result' => [$result]]);

        return $result;
    }

    /**
     * Create a new Image from a compressed image held as a string.
     *
     * @param string $buffer        The formatted image to open.
     * @param string $string_options Any text-style options to pass to the
     *     selected loader.
     * @param array  $options       Options to pass on to the load operation.
     *
     * @throws Exception
     *
     * @return Image A new Image.
     */
    public static function newFromBuffer(
        string $buffer,
        string $string_options = '',
        array $options = []
    ): Image {
        Utils::debugLog('newFromBuffer', [
            'instance' => null,
            'arguments' => [$buffer, $string_options, $options]
        ]);

        $loader = self::findLoadBuffer($buffer);
        if ($loader == null) {
            Config::error();
        }

        if (strlen($string_options) != 0) {
            $options = array_merge([
                "string_options" => $string_options,
            ], $options);
        }

        $result = VipsOperation::call($loader, null, [$buffer], $options);

        Utils::debugLog('newFromBuffer', ['result' => $result]);

        return $result;
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
     * @throws Exception
     *
     * @return Image A new Image.
     */
    public static function newFromArray(
        array $array,
        float $scale = 1.0,
        float $offset = 0.0
    ): Image {
        Utils::debugLog('newFromArray', [
            'instance' => null,
            'arguments' => [$array, $scale, $offset]
        ]);

        if (!self::is2D($array)) {
            $array = [$array];
        }

        $height = count($array);
        $width = count($array[0]);

        $n = $width * $height;
        $ctype = \FFI::arrayType(\FFI::type("double"), [$n]);
        $a = \FFI::new($ctype, true, true);
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $a[$x + $y * $width] = $array[$y][$x];
            }
        }

        $pointer = Config::ffi()->
            vips_image_new_matrix_from_array($width, $height, $a, $n);
        if ($pointer == null) {
            Config::error();
        }
        $result = new Image($pointer);

        $result->setType(Config::gtypes("gdouble"), 'scale', $scale);
        $result->setType(Config::gtypes("gdouble"), 'offset', $offset);

        Utils::debugLog('newFromArray', ['result' => $result]);

        return $result;
    }

    /**
     * Wraps an Image around an area of memory containing a C-style array.
     *
     * @param mixed  $data   C-style array.
     * @param int    $width  Image width in pixels.
     * @param int    $height Image height in pixels.
     * @param int    $bands  Number of bands.
     * @param string $format Band format. (@see BandFormat)
     *
     * @throws Exception
     *
     * @return Image A new Image.
     */
    public static function newFromMemory(
        mixed  $data,
        int $width,
        int $height,
        int $bands,
        string $format
    ): Image {
        Utils::debugLog('newFromMemory', [
            'instance' => null,
            'arguments' => [$data, $width, $height, $bands, $format]
        ]);

        /* Take a copy of the memory area to avoid lifetime issues.
         *
         * TODO add a references system instead, see pyvips.
         */
        $pointer = Config::ffi()->vips_image_new_from_memory_copy(
            $data,
            strlen($data),
            $width,
            $height,
            $bands,
            $format
        );
        if ($pointer == null) {
            Config::error();
        }

        $result = new Image($pointer);

        Utils::debugLog('newFromMemory', ['result' => $result]);

        return $result;
    }

    /**
     * Deprecated thing to make an interpolator.
     *
     * See Interpolator::newFromName() for the new thing.
     */
    public static function newInterpolator(string $name)
    {
        return Interpolate::newFromName($name);
    }

    /**
     * Create a new image from a constant.
     *
     * The new image has the same width, height, format, interpretation, xres,
     * yres, xoffset, yoffset as $this, but each pixel has the constant value
     * $value.
     *
     * Pass a single number to make a one-band image, pass an array of numbers
     * to make an N-band image.
     *
     * @param mixed $value The value to set each pixel to.
     *
     * @throws Exception
     *
     * @return Image A new Image.
     */
    public function newFromImage($value): Image
    {
        Utils::debugLog('newFromImage', [
            'instance' => $this,
            'arguments' => [$value]
        ]);

        $pixel = static::black(1, 1)->add($value)->cast($this->format);
        $image = $pixel->embed(
            0,
            0,
            $this->width,
            $this->height,
            ['extend' => Extend::COPY]
        );
        $image = $image->copy([
            'interpretation' => $this->interpretation,
            'xres' => $this->xres,
            'yres' => $this->yres,
            'xoffset' => $this->xoffset,
            'yoffset' => $this->yoffset
        ]);

        Utils::debugLog('newFromImage', ['result' => $image]);

        return $image;
    }

    /**
     * Write an image to a file.
     *
     * @param string $filename The file to write the image to.
     * @param array  $options  Any options to pass on to the selected save
     *     operation.
     *
     * @throws Exception
     *
     * @return void
     */
    public function writeToFile(string $name, array $options = []): void
    {
        Utils::debugLog('writeToFile', [
            'instance' => $this,
            'name' => $name,
            'options' => $options
        ]);

        $filename = Config::filenameGetFilename($name);
        $string_options = Config::filenameGetOptions($name);

        $saver = Config::ffi()->vips_foreign_find_save($filename);
        if ($saver == "") {
            Config::error();
        }

        if (strlen($string_options) != 0) {
            $options = array_merge([
                "string_options" => $string_options,
            ], $options);
        }

        $result = VipsOperation::call($saver, $this, [$filename], $options);

        Utils::debugLog('writeToFile', ['result' => $result]);

        if ($result === -1) {
            Config::error();
        }
    }

    /**
     * Write an image to a formatted string.
     *
     * @param string $suffix  The file type suffix, eg. ".jpg".
     * @param array  $options Any options to pass on to the selected save
     *     operation.
     *
     * @throws Exception
     *
     * @return string The formatted image.
     */
    public function writeToBuffer(string $suffix, array $options = []): string
    {
        Utils::debugLog('writeToBuffer', [
            'instance' => $this,
            'arguments' => [$suffix, $options]
        ]);

        $filename = Config::filenameGetFilename($suffix);
        $string_options = Config::filenameGetOptions($suffix);

        $saver = Config::ffi()->vips_foreign_find_save_buffer($filename);
        if ($saver == "") {
            Config::error();
        }

        if (strlen($string_options) != 0) {
            $options = array_merge([
                "string_options" => $string_options,
            ], $options);
        }

        $result = VipsOperation::call($saver, $this, [], $options);

        Utils::debugLog('writeToBuffer', ['result' => $result]);

        return $result;
    }

    /**
     * Write an image to a large memory array.
     *
     * @throws Exception
     *
     * @return string The memory array.
     */
    public function writeToMemory(): string
    {
        Utils::debugLog('writeToMemory', [
            'instance' => $this,
            'arguments' => []
        ]);

        $ctype = \FFI::arrayType(\FFI::type("size_t"), [1]);
        $p_size = \FFI::new($ctype);

        $pointer = Config::ffi()->
            vips_image_write_to_memory($this->pointer, $p_size);
        if ($pointer == null) {
            Config::error();
        }

        // string() takes a copy
        $result = \FFI::string($pointer, $p_size[0]);

        Config::ffi()->g_free($pointer);

        Utils::debugLog('writeToMemory', ['result' => $result]);

        return $result;
    }

    /**
     * Write an image to a PHP array.
     *
     * Pixels are written as a simple one-dimensional array, for example, if
     * you write:
     *
     * ```php
     * $result = $image->crop(100, 100, 10, 1)->writeToArray();
     * ```
     *
     * This will crop out 10 pixels and write them to the array. If `$image`
     * is an RGB image, then `$array` will contain 30 numbers, with the first
     * three being R, G and B for the first pixel.
     *
     * You'll need to slice and repack the array if you want more dimensions.
     *
     * This method is much faster than repeatedly calling `getpoint()`. It
     * will use a lot of memory.
     *
     * @throws Exception
     *
     * @return array The pixel values as a PHP array.
     */
    public function writeToArray(): array
    {
        Utils::debugLog('writeToArray', [
            'instance' => $this,
            'arguments' => []
        ]);

        $ctype = \FFI::arrayType(\FFI::type("size_t"), [1]);
        $p_size = \FFI::new($ctype);

        $pointer = Config::ffi()->
            vips_image_write_to_memory($this->pointer, $p_size);
        if ($pointer == null) {
            Config::error();
        }

        // wrap pointer up as a C array of the right type
        $n = $this->width * $this->height * $this->bands;
        $type_name = Config::ftypes($this->format);
        $ctype = \FFI::arrayType(\FFI::type($type_name), [$n]);
        $array = \FFI::cast($ctype, $pointer);

        // copy to PHP memory as a flat array
        $result = [];
        for ($i = 0; $i < $n; $i++) {
            $result[] = $array[$i];
        }

        // the vips result is not PHP memory, so we must free it
        Config::ffi()->g_free($pointer);

        Utils::debugLog('writeToArray', ['result' => $result]);

        return $result;
    }

    /**
     * Copy to memory.
     *
     * An area of memory large enough to hold the complete image is allocated,
     * the image is rendered into it, and a new Image is returned which wraps
     * this memory area.
     *
     * This is useful for ending a pipeline and starting a new random access
     * one, but can obviously use a lot of memory if the image is large.
     *
     * @throws Exception
     *
     * @return Image A new Image.
     */
    public function copyMemory(): Image
    {
        Utils::debugLog('copyMemory', [
            'instance' => $this,
            'arguments' => []
        ]);

        $pointer = Config::ffi()->vips_image_copy_memory($this->pointer);
        if ($pointer == null) {
            Config::error();
        }
        $result = new Image($pointer);

        Utils::debugLog('copyMemory', ['result' => $result]);

        return $result;
    }

    /**
     * Get any property from the underlying image.
     *
     * @param string $name The property name.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Set any property on the underlying image.
     *
     * @param string $name  The property name.
     * @param mixed  $value The value to set for this property.
     *
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $this->set($name, $value);
    }

    /**
     * Check if the GType of a property from the underlying image exists.
     *
     * @param string $name The property name.
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->getType($name) != 0;
    }

    /**
     * Get any property from the underlying image.
     *
     * This is handy for fields whose name
     * does not match PHP's variable naming conventions, like `'exif-data'`.
     *
     * It will throw an exception if $name does not exist. Use Image::getType()
     * to test for the existence of a field.
     *
     * @param string $name The property name.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function get(string $name)
    {
        $gvalue = new GValue();
        if (Config::ffi()->
            vips_image_get($this->pointer, $name, $gvalue->pointer) != 0) {
            Config::error();
        }

        return $gvalue->get();
    }

    /**
     * Get the GType of a property from the underlying image. GTypes are
     * integer type identifiers. This function will return 0 if the field does
     * not exist.
     *
     * @param string $name The property name.
     *
     * @return integer
     */
    public function getType(string $name): int
    {
        return Config::ffi()->vips_image_get_typeof($this->pointer, $name);
    }

    /**
     * A deprecated synonym for getType().
     *
     * @param string $name The property name.
     *
     * @return integer
     */
    public function typeOf(string $name): int
    {
        return $this->getType($name);
    }

    /**
     * Set any property on the underlying image.
     *
     * This is handy for fields whose name
     * does not match PHP's variable naming conventions, like `'exif-data'`.
     *
     * @param string $name  The property name.
     * @param mixed  $value The value to set for this property.
     *
     * @throws Exception
     *
     * @return void
     */
    public function set(string $name, $value): void
    {
        $gvalue = new GValue();
        $gtype = $this->getType($name);

        /* If this is not a known field, guess a sensible type from the value.
         */
        if ($gtype == 0) {
            if (is_array($value)) {
                if (is_int($value[0])) {
                    $gtype = Config::gtypes("VipsArrayInt");
                } elseif (is_float($value[0])) {
                    $gtype = Config::gtypes("VipsArrayDouble");
                } else {
                    $gtype = Config::gtypes("VipsArrayImage");
                }
            } elseif (is_int($value)) {
                $gtype = Config::gtypes("gint");
            } elseif (is_float($value)) {
                $gtype = Config::gtypes("gdouble");
            } elseif (is_string($value)) {
                $gtype = Config::gtypes("VipsRefString");
            } else {
                $gtype = Config::gtypes("VipsImage");
            }
        }

        $gvalue->setType($gtype);
        $gvalue->set($value);

        Config::ffi()->vips_image_set($this->pointer, $name, $gvalue->pointer);
    }

    /**
     * Set the type and value for any property on the underlying image.
     *
     * This is useful if the type of the property cannot be determined from the
     * php type of the value.
     *
     * Pass the type name directly, or use Utils::typeFromName() to look up
     * types by name.
     *
     * @param string|int $type  The type of the property.
     * @param string     $name  The property name.
     * @param mixed      $value The value to set for this property.
     *
     * @throws Exception
     *
     * @return void
     */
    public function setType($type, string $name, $value): void
    {
        $gvalue = new GValue();
        $gvalue->setType($type);
        $gvalue->set($value);
        Config::ffi()->vips_image_set($this->pointer, $name, $gvalue->pointer);
    }

    /**
     * Remove a field from the underlying image.
     *
     * @param string $name The property name.
     *
     * @throws Exception
     *
     * @return void
     */
    public function remove(string $name): void
    {
        if (!Config::ffi()->vips_image_remove($this->pointer, $name)) {
            Config::error();
        }
    }

    /**
     * Makes a string-ified version of the Image.
     *
     * @return string
     */
    public function __toString()
    {
        $array = [
            'width' => $this->width,
            'height' => $this->height,
            'bands' => $this->bands,
            'format' => $this->format,
            'interpretation' => $this->interpretation,
        ];

        return json_encode($array);
    }

    /**
     * Call any vips operation as an instance method.
     *
     * @param string $name      The thing we call.
     * @param array  $arguments The arguments to the thing.
     *
     * @throws Exception
     *
     * @return mixed The result.
     */
    public function __call(string $name, array $arguments)
    {
        return VipsOperation::callBase($name, $this, $arguments);
    }

    /**
     * Call any vips operation as a class method.
     *
     * @param string $name      The thing we call.
     * @param array  $arguments The arguments to the thing.
     *
     * @throws Exception
     *
     * @return mixed The result.
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return VipsOperation::callBase($name, null, $arguments);
    }

    /**
     * Does this image have an alpha channel?
     *
     * Uses colour space interpretation with number of channels to guess
     * this.
     *
     * @return bool indicating if this image has an alpha channel.
     */
    public function hasAlpha(): bool
    {
        return $this->bands === 2 ||
            ($this->bands === 4 &&
                $this->interpretation !== Interpretation::CMYK) ||
            $this->bands > 4;
    }

    /**
     * Does band exist in image.
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
     * Get band from image.
     *
     * @param mixed $offset The index to fetch.
     *
     * @throws Exception
     *
     * @return Image|null the extracted band or null.
     */
    public function offsetGet($offset): ?Image
    {
        return $this->offsetExists($offset) ?
            $this->extract_band($offset) : null;
    }

    /**
     * Set a band.
     *
     * Use `$image[1] = $other_image;' to remove band 1 from this image,
     * replacing it with all the bands in `$other_image`.
     *
     * Use `$image[] = $other_image;' to append all the bands in `$other_image`
     * to `$image`.
     *
     * Use `$image[-1] = $other_image;` to prepend all the bands in
     * `$other_image` to `$image`.
     *
     * You can use constants or arrays in place of `$other_image`. Use `$image[]
     * = 255;` to append a constant 255 band, for example, or `$image[1]
     * = [1, 2];` to replace band 1 with two constant bands.
     *
     * @param int   $offset The index to set.
     * @param Image $value  The band to insert
     *
     * @throws \BadMethodCallException if the offset is not integer or null
     * @throws Exception
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        // no offset means append
        if ($offset === null) {
            $offset = $this->bands;
        }

        if (!is_int($offset)) {
            throw new \BadMethodCallException('Image::offsetSet: ' .
                'offset is not integer or null');
        }

        // number of bands to the left and right of $value
        $n_left = min($this->bands, max(0, $offset));
        $n_right = min($this->bands, max(0, $this->bands - 1 - $offset));
        $offset = $this->bands - $n_right;

        // if we are setting a constant as the first element, we must expand it
        // to an image, since bandjoin must have an image as the first argument
        if ($n_left === 0 && !($value instanceof Image)) {
            $value = $this->imageize($value);
        }

        $components = [];
        if ($n_left > 0) {
            $components[] = $this->extract_band(0, ['n' => $n_left]);
        }
        $components[] = $value;
        if ($n_right > 0) {
            $components[] = $this->extract_band($offset, ['n' => $n_right]);
        }

        $head = array_shift($components);
        $joined = $head->bandjoin($components);

        /* Overwrite our pointer with the pointer from the new, joined object.
         * We have to adjust the refs, yuk!
         */
        $joined->ref();
        $this->unref();
        $this->pointer = $joined->pointer;
    }

    /**
     * Remove a band from an image.
     *
     * @param int $offset The index to remove.
     *
     * @throws \BadMethodCallException if there is only one band left in
     * the image
     * @throws Exception
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        if (is_int($offset) && $offset >= 0 && $offset < $this->bands) {
            if ($this->bands === 1) {
                throw new \BadMethodCallException('Image::offsetUnset: ' .
                    'cannot delete final band');
            }

            $components = [];
            if ($offset > 0) {
                $components[] = $this->extract_band(0, ['n' => $offset]);
            }
            if ($offset < $this->bands - 1) {
                $components[] = $this->extract_band(
                    $offset + 1,
                    ['n' => $this->bands - 1 - $offset]
                );
            }

            $head = array_shift($components);
            if (empty($components)) {
                $head->ref();
                $this->unref();
                $this->pointer = $head->pointer;
            } else {
                $new_image = $head->bandjoin($components);
                $new_image->ref();
                $this->unref();
                $this->pointer = $new_image->pointer;
            }
        }
    }

    /**
     * Add $other to this image.
     *
     * @param mixed $other   The thing to add to this image.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function add($other, array $options = []): Image
    {
        if (self::isImageish($other)) {
            return VipsOperation::call('add', $this, [$other], $options);
        } else {
            return $this->linear(1, $other, $options);
        }
    }

    /**
     * Subtract $other from this image.
     *
     * @param mixed $other   The thing to subtract from this image.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function subtract($other, array $options = []): Image
    {
        if (self::isImageish($other)) {
            return VipsOperation::call('subtract', $this, [$other], $options);
        } else {
            $other = self::mapNumeric($other, function ($value) {
                return -1 * $value;
            });
            return $this->linear(1, $other, $options);
        }
    }

    /**
     * Multiply this image by $other.
     *
     * @param mixed $other   The thing to multiply this image by.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function multiply($other, array $options = []): Image
    {
        if (self::isImageish($other)) {
            return VipsOperation::call('multiply', $this, [$other], $options);
        } else {
            return $this->linear($other, 0, $options);
        }
    }

    /**
     * Divide this image by $other.
     *
     * @param mixed $other   The thing to divide this image by.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function divide($other, array $options = []): Image
    {
        if (self::isImageish($other)) {
            return VipsOperation::call('divide', $this, [$other], $options);
        } else {
            $other = self::mapNumeric($other, function ($value) {
                return $value ** -1;
            });
            return $this->linear($other, 0, $options);
        }
    }

    /**
     * Remainder of this image and $other.
     *
     * @param mixed $other   The thing to take the remainder with.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function remainder($other, array $options = []): Image
    {
        if (self::isImageish($other)) {
            return VipsOperation::call('remainder', $this, [$other], $options);
        } else {
            return VipsOperation::call(
                'remainder_const',
                $this,
                [$other],
                $options
            );
        }
    }

    /**
     * Find $this to the power of $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function pow($other, array $options = []): Image
    {
        return self::callEnum($other, 'math2', OperationMath2::POW, $options);
    }

    /**
     * Find $other to the power of $this.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function wop($other, array $options = []): Image
    {
        return self::callEnum($other, 'math2', OperationMath2::WOP, $options);
    }

    /**
     * Shift $this left by $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function lshift($other, array $options = []): Image
    {
        return self::callEnum($other, 'boolean', OperationBoolean::LSHIFT, $options);
    }

    /**
     * Shift $this right by $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function rshift($other, array $options = []): Image
    {
        return self::callEnum(
            $other,
            'boolean',
            OperationBoolean::RSHIFT,
            $options
        );
    }

    /**
     * Bitwise AND of $this and $other. This has to be called ->andimage()
     * rather than ->and() to avoid confusion in phpdoc.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function andimage($other, array $options = []): Image
    {
        // phpdoc hates OperationBoolean::AND, so use the string form here
        return self::callEnum($other, 'boolean', 'and', $options);
    }

    /**
     * Bitwise OR of $this and $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function orimage($other, array $options = []): Image
    {
        // phpdoc hates OperationBoolean::OR, so use the string form here
        return self::callEnum($other, 'boolean', 'or', $options);
    }

    /**
     * Bitwise EOR of $this and $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function eorimage($other, array $options = []): Image
    {
        return self::callEnum(
            $other,
            'boolean',
            OperationBoolean::EOR,
            $options
        );
    }

    /**
     * 255 where $this is more than $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function more($other, array $options = []): Image
    {
        return self::callEnum(
            $other,
            'relational',
            OperationRelational::MORE,
            $options
        );
    }

    /**
     * 255 where $this is more than or equal to $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function moreEq($other, array $options = []): Image
    {
        return self::callEnum(
            $other,
            'relational',
            OperationRelational::MOREEQ,
            $options
        );
    }

    /**
     * 255 where $this is less than $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function less($other, array $options = []): Image
    {
        return self::callEnum(
            $other,
            'relational',
            OperationRelational::LESS,
            $options
        );
    }

    /**
     * 255 where $this is less than or equal to $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function lessEq($other, array $options = []): Image
    {
        return self::callEnum(
            $other,
            'relational',
            OperationRelational::LESSEQ,
            $options
        );
    }

    /**
     * 255 where $this is equal to $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function equal($other, array $options = []): Image
    {
        return self::callEnum(
            $other,
            'relational',
            OperationRelational::EQUAL,
            $options
        );
    }

    /**
     * 255 where $this is not equal to $other.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function notEq($other, array $options = []): Image
    {
        return self::callEnum(
            $other,
            'relational',
            OperationRelational::NOTEQ,
            $options
        );
    }

    /**
     * Join $this and $other bandwise.
     *
     * @param mixed $other   The right-hand side of the operator.
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function bandjoin($other, array $options = []): Image
    {
        /* Allow a single unarrayed value as well.
         */
        if ($other instanceof Image) {
            $other = [$other];
        } else {
            $other = (array) $other;
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
            return VipsOperation::call(
                'bandjoin_const',
                $this,
                [$other],
                $options
            );
        } else {
            return VipsOperation::call(
                'bandjoin',
                null,
                [array_merge([$this], $other)],
                $options
            );
        }
    }

    /**
     * Split $this into an array of single-band images.
     *
     * @param array $options An array of options to pass to the operation.
     *
     * @throws Exception
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
     * @throws Exception
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
        if ($other instanceof Image) {
            $other = [$other];
        } else {
            $other = (array) $other;
        }

        return VipsOperation::call('bandrank', $this, $other, $options);
    }

    /**
     * Composite $other on top of $this with $mode.
     *
     * @param mixed $other          The overlay.
     * @param BlendMode|array $mode The mode to composite with.
     * @param array $options        An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function composite($other, $mode, array $options = []): Image
    {
        /* Allow a single unarrayed value as well.
         */
        if ($other instanceof Image) {
            $other = [$other];
        } else {
            $other = (array) $other;
        }

        if (!is_array($mode)) {
            $mode = [$mode];
        }

        # composite takes an arrayint, but it's really an array of blend modes
        # gvalue doesn't know this, so we must do name -> enum value mapping
        $mode = array_map(function ($x) {
            return GValue::toEnum(Config::gtypes("VipsBlendMode"), $x);
        }, $mode);

        return VipsOperation::call(
            'composite',
            null,
            [array_merge([$this], $other), $mode],
            $options
        );
    }

    /**
     * Position of max is awkward with plain self::max.
     *
     * @throws Exception
     *
     * @return array (float, int, int) The value and position of the maximum.
     */
    public function maxpos(): array
    {
        $result = $this->max(['x' => true, 'y' => true]);
        $out = $result['out'];
        $x = $result['x'];
        $y = $result['y'];

        return [$out, $x, $y];
    }

    /**
     * Position of min is awkward with plain self::max.
     *
     * @throws Exception
     *
     * @return array (float, int, int) The value and position of the minimum.
     */
    public function minpos(): array
    {
        $result = $this->min(['x' => true, 'y' => true]);
        $out = $result['out'];
        $x = $result['x'];
        $y = $result['y'];

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
     * @throws Exception
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
            $then = $match_image->imageize($then);
        }

        if (!($else instanceof Image)) {
            $else = $match_image->imageize($else);
        }

        return VipsOperation::call(
            'ifthenelse',
            $this,
            [$then, $else],
            $options
        );
    }

    /**
     * Return the largest integral value not greater than the argument.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function floor(): Image
    {
        return $this->round(OperationRound::FLOOR);
    }

    /**
     * Return the smallest integral value not less than the argument.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function ceil(): Image
    {
        return $this->round(OperationRound::CEIL);
    }

    /**
     * Return the nearest integral value.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function rint(): Image
    {
        return $this->round(OperationRound::RINT);
    }

    /**
     * AND image bands together.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function bandand(): Image
    {
        // phpdoc hates OperationBoolean::AND, so use the string form here
        return $this->bandbool('and');
    }

    /**
     * OR image bands together.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function bandor(): Image
    {
        // phpdoc hates OperationBoolean::OR, so use the string form here
        return $this->bandbool('or');
    }

    /**
     * EOR image bands together.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function bandeor(): Image
    {
        return $this->bandbool(OperationBoolean::EOR);
    }

    /**
     * Return the real part of a complex image.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function real(): Image
    {
        return $this->complexget(OperationComplexget::REAL);
    }

    /**
     * Return the imaginary part of a complex image.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function imag(): Image
    {
        return $this->complexget(OperationComplexget::IMAG);
    }

    /**
     * Return an image converted to polar coordinates.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function polar(): Image
    {
        return self::runCmplx(function ($image) {
            return $image->complex(OperationComplex::POLAR);
        }, $this);
    }

    /**
     * Return an image converted to rectangular coordinates.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function rect(): Image
    {
        return self::runCmplx(function ($image) {
            return $image->complex(OperationComplex::RECT);
        }, $this);
    }

    /**
     * Return the complex conjugate of an image.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function conj(): Image
    {
        return $this->complex(OperationComplex::CONJ);
    }

    /**
     * Find the cross-phase of this image with $other.
     *
     * @param mixed $other   The thing to cross-phase by.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function crossPhase($other): Image
    {
        return $this->complex2($other, OperationComplex2::CROSS_PHASE);
    }

    /**
     * Return the sine of an image in degrees.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function sin(): Image
    {
        return $this->math(OperationMath::SIN);
    }

    /**
     * Return the cosine of an image in degrees.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function cos(): Image
    {
        return $this->math(OperationMath::COS);
    }

    /**
     * Return the tangent of an image in degrees.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function tan(): Image
    {
        return $this->math(OperationMath::TAN);
    }

    /**
     * Return the inverse sine of an image in degrees.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function asin(): Image
    {
        return $this->math(OperationMath::ASIN);
    }

    /**
     * Return the inverse cosine of an image in degrees.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function acos(): Image
    {
        return $this->math(OperationMath::ACOS);
    }

    /**
     * Return the inverse tangent of an image in degrees.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function atan(): Image
    {
        return $this->math(OperationMath::ATAN);
    }

    /**
     * Return the natural log of an image.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function log(): Image
    {
        return $this->math(OperationMath::LOG);
    }

    /**
     * Return the log base 10 of an image.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function log10(): Image
    {
        return $this->math(OperationMath::LOG10);
    }

    /**
     * Return e ** pixel.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function exp(): Image
    {
        return $this->math(OperationMath::EXP);
    }

    /**
     * Return 10 ** pixel.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function exp10(): Image
    {
        return $this->math(OperationMath::EXP10);
    }

    /**
     * Erode with a structuring element.
     *
     * @param mixed $mask Erode with this structuring element.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function erode($mask): Image
    {
        return $this->morph($mask, OperationMorphology::ERODE);
    }

    /**
     * Dilate with a structuring element.
     *
     * @param mixed $mask Dilate with this structuring element.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function dilate($mask): Image
    {
        return $this->morph($mask, OperationMorphology::DILATE);
    }

    /**
     * $size x $size median filter.
     *
     * @param int $size Size of median filter.
     *
     * @throws Exception
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
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function fliphor(): Image
    {
        return $this->flip(Direction::HORIZONTAL);
    }

    /**
     * Flip vertically.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function flipver(): Image
    {
        return $this->flip(Direction::VERTICAL);
    }

    /**
     * Rotate 90 degrees clockwise.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function rot90(): Image
    {
        return $this->rot(Angle::D90);
    }

    /**
     * Rotate 180 degrees.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function rot180(): Image
    {
        return $this->rot(Angle::D180);
    }

    /**
     * Rotate 270 degrees clockwise.
     *
     * @throws Exception
     *
     * @return Image A new image.
     */
    public function rot270(): Image
    {
        return $this->rot(Angle::D270);
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
