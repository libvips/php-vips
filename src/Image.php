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
 * library](https://jcupitt.github.io/libvips/).
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
 * API](https://jcupitt.github.io/libvips/API/current).
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
     * Map load nicknames to canonical names. Regenerate this table with
     * something like:
     *
     * $ vips -l foreign | grep -i load | awk '{ print $2, $1; }'
     *
     * Plus a bit of editing.
     *
     * @internal
     */
    private static $nicknameToCanonical = [
        'csvload' => 'VipsForeignLoadCsv',
        'matrixload' => 'VipsForeignLoadMatrix',
        'rawload' => 'VipsForeignLoadRaw',
        'vipsload' => 'VipsForeignLoadVips',
        'analyzeload' => 'VipsForeignLoadAnalyze',
        'ppmload' => 'VipsForeignLoadPpm',
        'radload' => 'VipsForeignLoadRad',
        'pdfload' => 'VipsForeignLoadPdfFile',
        'pdfload_buffer' => 'VipsForeignLoadPdfBuffer',
        'svgload' => 'VipsForeignLoadSvgFile',
        'svgload_buffer' => 'VipsForeignLoadSvgBuffer',
        'gifload' => 'VipsForeignLoadGifFile',
        'gifload_buffer' => 'VipsForeignLoadGifBuffer',
        'pngload' => 'VipsForeignLoadPng',
        'pngload_buffer' => 'VipsForeignLoadPngBuffer',
        'matload' => 'VipsForeignLoadMat',
        'jpegload' => 'VipsForeignLoadJpegFile',
        'jpegload_buffer' => 'VipsForeignLoadJpegBuffer',
        'webpload' => 'VipsForeignLoadWebpFile',
        'webpload_buffer' => 'VipsForeignLoadWebpBuffer',
        'tiffload' => 'VipsForeignLoadTiffFile',
        'tiffload_buffer' => 'VipsForeignLoadTiffBuffer',
        'magickload' => 'VipsForeignLoadMagickFile',
        'magickload_buffer' => 'VipsForeignLoadMagickBuffer',
        'fitsload' => 'VipsForeignLoadFits',
        'openexrload' => 'VipsForeignLoadOpenexr'
    ];

    /**
     * Combine takes an array of blend modes, passed to libvips as an array of
     * int. Because libvips does now know they should be enums, we have to do
     * the string->int conversion ourselves. We ought to introspect to find the
     * mapping, but until we have the machinery for that, we just hardwire the
     * mapping here.
     *
     * @internal
     */
    private static $blendModeToInt = [
        BlendMode::CLEAR => 0,
        BlendMode::SOURCE => 1,
        BlendMode::OVER => 2,
        BlendMode::IN => 3,
        BlendMode::OUT => 4,
        BlendMode::ATOP => 5,
        BlendMode::DEST => 6,
        BlendMode::DEST_OVER => 7,
        BlendMode::DEST_IN => 8,
        BlendMode::DEST_OUT => 9,
        BlendMode::DEST_ATOP => 10,
        BlendMode::XOR1 => 11,
        BlendMode::ADD => 12,
        BlendMode::SATURATE => 13,
        BlendMode::MULTIPLY => 14,
        BlendMode::SCREEN => 15,
        BlendMode::OVERLAY => 16,
        BlendMode::DARKEN => 17,
        BlendMode::LIGHTEN => 18,
        BlendMode::COLOUR_DODGE => 19,
        BlendMode::COLOUR_BURN => 20,
        BlendMode::HARD_LIGHT => 21,
        BlendMode::SOFT_LIGHT => 22,
        BlendMode::DIFFERENCE => 23,
        BlendMode::EXCLUSION => 24
    ];

    /**
     * The resource for the underlying VipsImage.
     *
     * @internal
     */
    private $image;

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
        $this->image = $image;
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
    private static function isImageish($value): bool
    {
        return self::is2D($value) || $value instanceof Image;
    }

    /**
     * Turn a constant (eg. 1, '12', [1, 2, 3], [[1]]) into an image using
     * match_image as a guide.
     *
     * @param Image $match_image Use this image as a guide.
     * @param mixed $value       Turn this into an image.
     *
     * @throws Exception
     *
     * @return Image The image we created.
     *
     * @internal
     */
    private static function imageize(Image $match_image, $value): Image
    {
        if (self::is2D($value)) {
            $result = self::newFromArray($value);
        } else {
            $result = $match_image->newFromImage($value);
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
    private static function unwrap(array $result): array
    {
        array_walk_recursive($result, function (&$value) {
            if ($value instanceof Image) {
                $value = $value->image;
            }
        });

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
    private static function isImage($value): bool
    {
        return is_resource($value) &&
            get_resource_type($value) === 'GObject';
    }

    /**
     * Wrap up the result of a vips_ call ready to return it to PHP. We do
     * two things:
     *
     * - If the array is a singleton, we strip it off. For example, many
     *   operations return a single result and there's no sense handling
     *   this as an array of values, so we transform ['out' => x] -> x.
     *
     * - Any VipsImage resources are rewrapped as instances of Image.
     *
     * @param mixed $result Wrap this up.
     *
     * @return mixed $result, but wrapped up as a php class.
     *
     * @internal
     */
    private static function wrapResult($result)
    {
        if (!is_array($result)) {
            $result = ['x' => $result];
        }

        array_walk_recursive($result, function (&$item) {
            if (self::isImage($item)) {
                $item = new self($item);
            }
        });

        if (count($result) === 1) {
            $result = array_shift($result);
        }

        return $result;
    }

    /**
     * Throw a vips error as an exception.
     *
     * @throws Exception
     *
     * @return void
     *
     * @internal
     */
    private static function errorVips(): void
    {
        $message = Init::ffi()->vips_error_buffer();
        Init::ffi()->vips_error_buffer_clear();
        $exception = new Exception($message);
        Utils::errorLog($message, $exception);
        throw $exception;
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
     * @throws Exception
     *
     * @return void
     *
     * @internal
     */
    private static function errorIsArray($result): void
    {
        if (!is_array($result)) {
            self::errorVips();
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

        $filename = Init::ffi()->vips_filename_get_filename($name);
        $string_options = Init::ffi()->vips_filename_get_options($name);
        $options = self::unwrap($options);

        $loader = Init::ffi()->vips_foreign_find_load($filename);
        if ($loader == "") {
            self::errorVips();
        }

        $result = self::callBase($loader, $filename, 
            array_merge(["string_options" => $string_options], $options));
        self::errorIsArray($result);
        $result = self::wrapResult($result);

        Utils::debugLog('newFromFile', ['result' => $result]);

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
            'arguments' => [$buffer, $option_string, $options]
        ]);

        $options = self::unwrap($options);
        $loader = Init::ffi()->vips_foreign_find_load_buffer($buffer);
        if (\FFI::isNULL($loader)) {
            self::errorVips();
        }
        $result = self::callBase($loader, $buffer, 
            array_merge(["string_options" => $string_options], $options));
        self::errorIsArray($result);
        $result = self::wrapResult($result);

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
        $a = Init::ffi()->new("double[]", $n);
        for($y = 0; $y < $height; $y++) {
            for($x = 0; $x < $width; $x++) {
                $a[$x + $y * $width] = $array[y][x];
            }
        }

        $result = Init::ffi()->
            vips_image_new_matrix_from_array($width, $height, $a, $n);
        if (\FFI::isNULL($result)) {
            self::errorVips();
        }
        $result = self::wrapResult($result);

        $image.set_type(GValue::gdouble_type, 'scale', $scale);
        $image.set_type(GValue::gdouble_type, 'offset', $offset);

        Utils::debugLog('newFromArray', ['result' => $result]);

        return image;
    }

    /**
     * Wraps an Image around an area of memory containing a C-style array.
     *
     * @param string $data   C-style array.
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
        string $data,
        int $width,
        int $height,
        int $bands,
        string $format
    ): Image {
        Utils::debugLog('newFromMemory', [
            'instance' => null,
            'arguments' => [$data, $width, $height, $bands, $format]
        ]);

        $result = Init::ffi()->
            vips_image_new_from_memory($data, $width, $height, $bands, $format);
        if (\FFI::isNULL($result)) {
            self::errorVips();
        }
        $result = self::wrapResult($result);

        Utils::debugLog('newFromMemory', ['result' => $result]);

        return $result;
    }

    /**
     * Make an interpolator from a name.
     *
     * @param string $name Name of the interpolator.
     * Possible interpolators are:
     *  - `'nearest'`: Use nearest neighbour interpolation.
     *  - `'bicubic'`: Use bicubic interpolation.
     *  - `'bilinear'`: Use bilinear interpolation (the default).
     *  - `'nohalo'`: Use Nohalo interpolation.
     *  - `'lbb'`: Use LBB interpolation.
     *  - `'vsqbs'`: Use the VSQBS interpolation.
     *
     * @return resource|null The interpolator, or null on error.
     */
    public static function newInterpolator(string $name)
    {
        Utils::debugLog('newInterpolator', [
            'instance' => null,
            'arguments' => [$name]
        ]);

        return Init::ffi()->vips_interpolate_new($name);
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

        $pixel = self::black(1, 1)->add($value)->cast($this->format);
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
    public function writeToFile(string $filename, array $options = []): void
    {
        Utils::debugLog('writeToFile', [
            'instance' => $this,
            'arguments' => [$filename, $options]
        ]);

        $options = self::unwrap($options);
        $result = vips_image_write_to_file($this->image, $filename, $options);
        if ($result === -1) {
            self::errorVips();
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

        $options = self::unwrap($options);
        $result = vips_image_write_to_buffer($this->image, $suffix, $options);
        if ($result === -1) {
            self::errorVips();
        }
        $result = self::wrapResult($result);

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

        $result = vips_image_write_to_memory($this->image);
        if ($result === -1) {
            self::errorVips();
        }

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

        $result = vips_image_write_to_array($this->image);
        if ($result === -1) {
            self::errorVips();
        }

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

        $result = vips_image_copy_memory($this->image);
        if ($result === -1) {
            self::errorVips();
        }
        $result = self::wrapResult($result);

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
        $result = vips_image_get($this->image, $name);
        self::errorIsArray($result);
        return self::wrapResult($result);
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
        vips_image_set($this->image, $name, $value);
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
        return $this->typeof($name) !== 0;
    }

    /**
     * Get any property from the underlying image.
     *
     * This is handy for fields whose name
     * does not match PHP's variable naming conventions, like `'exif-data'`.
     *
     * It will throw an exception if $name does not exist. Use Image::typeof()
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
        $result = vips_image_get($this->image, $name);
        self::errorIsArray($result);
        return self::wrapResult($result);
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
    public function typeof(string $name): int
    {
        return vips_image_get_typeof($this->image, $name);
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
        $result = vips_image_set($this->image, $name, $value);
        if ($result === -1) {
            self::errorVips();
        }
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
        $result = vips_image_set_type($this->image, $type, $name, $value);
        if ($result === -1) {
            self::errorVips();
        }
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
        $result = vips_image_remove($this->image, $name);
        if ($result === -1) {
            self::errorVips();
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
     * @throws Exception
     *
     * @return mixed The result(s) of the operation.
     */
    public static function callBase(
        string $name,
        ?Image $instance,
        array $arguments
    ) {
        Utils::debugLog($name, [
            'instance' => $instance,
            'arguments' => $arguments
        ]);

        $arguments = array_merge([$name, $instance], $arguments);

        $arguments = array_values(self::unwrap($arguments));
        $result = vips_call(...$arguments);
        self::errorIsArray($result);
        $result = self::wrapResult($result);

        Utils::debugLog($name, ['result' => $result]);

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
     * @throws Exception
     *
     * @return mixed The result(s) of the operation.
     */
    public static function call(
        string $name,
        ?Image $instance,
        array $arguments,
        array $options = []
    ) {
        /*
        echo "call: $name \n";
        echo "instance = \n";
        var_dump($instance);
        echo "arguments = \n";
        var_dump($arguments);
        echo "options = \n";
        var_dump($options);
         */

        return self::callBase($name, $instance, array_merge($arguments, [$options]));
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
            return self::call($base, $this, [$other, $op], $options);
        } else {
            return self::call($base . '_const', $this, [$op, $other], $options);
        }
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
        return self::callBase($name, $this, $arguments);
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
        return self::callBase($name, null, $arguments);
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
        return $this->offsetExists($offset) ? $this->extract_band($offset) : null;
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
            throw new \BadMethodCallException('Image::offsetSet: offset is not integer or null');
        }

        // number of bands to the left and right of $value
        $n_left = min($this->bands, max(0, $offset));
        $n_right = min($this->bands, max(0, $this->bands - 1 - $offset));
        $offset = $this->bands - $n_right;

        // if we are setting a constant as the first element, we must expand it
        // to an image, since bandjoin must have an image as the first argument
        if ($n_left === 0 && !($value instanceof Image)) {
            $value = self::imageize($this, $value);
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
        $this->image = $head->bandjoin($components)->image;
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
                throw new \BadMethodCallException('Image::offsetUnset: cannot delete final band');
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
                $this->image = $head->image;
            } else {
                $this->image = $head->bandjoin($components)->image;
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
            return self::call('add', $this, [$other], $options);
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
            return self::call('subtract', $this, [$other], $options);
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
            return self::call('multiply', $this, [$other], $options);
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
            return self::call('divide', $this, [$other], $options);
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
            return self::call('remainder', $this, [$other], $options);
        } else {
            return self::call('remainder_const', $this, [$other], $options);
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
        return $this->callEnum($other, 'math2', OperationMath2::POW, $options);
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
        return $this->callEnum($other, 'math2', OperationMath2::WOP, $options);
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
        return $this->callEnum($other, 'boolean', OperationBoolean::LSHIFT, $options);
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
        return $this->callEnum($other, 'boolean', OperationBoolean::RSHIFT, $options);
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
        return $this->callEnum($other, 'boolean', 'and', $options);
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
        return $this->callEnum($other, 'boolean', 'or', $options);
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
        return $this->callEnum($other, 'boolean', OperationBoolean::EOR, $options);
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
        return $this->callEnum($other, 'relational', OperationRelational::MORE, $options);
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
        return $this->callEnum($other, 'relational', OperationRelational::MOREEQ, $options);
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
        return $this->callEnum($other, 'relational', OperationRelational::LESS, $options);
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
        return $this->callEnum($other, 'relational', OperationRelational::LESSEQ, $options);
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
        return $this->callEnum($other, 'relational', OperationRelational::EQUAL, $options);
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
        return $this->callEnum($other, 'relational', OperationRelational::NOTEQ, $options);
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
            return self::call('bandjoin_const', $this, [$other], $options);
        } else {
            return self::call(
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

        return self::call('bandrank', $this, $other, $options);
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

        $mode = array_map(function ($x) {
            // Use BlendMode::OVER if a non-existent value is given.
            return self::$blendModeToInt[$x] ?? BlendMode::OVER;
        }, $mode);

        return self::call(
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
            $then = self::imageize($match_image, $then);
        }

        if (!($else instanceof Image)) {
            $else = self::imageize($match_image, $else);
        }

        return self::call('ifthenelse', $this, [$then, $else], $options);
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
