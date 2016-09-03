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
 * @category   Images
 * @package    Vips
 * @author     John Cupitt <jcupitt@gmail.com>
 * @copyright  2016 John Cupitt
 * @license    MIT
 * @version    0.1.0
 * @link       https://github.com/jcupitt/php-vips
 */

namespace Vips;

if (!extension_loaded("vips")) {
    dl('vips.' . PHP_SHLIB_SUFFIX);
}

/**
 * ImageClass represents an image. 
 *
 * @category   Images
 * @package    Vips
 * @author     John Cupitt <jcupitt@gmail.com>
 * @copyright  2016 John Cupitt
 * @license    MIT
 * @version    0.1.0
 */
class ImageClass implements \ArrayAccess
{
    /* The resource for the underlying VipsImage.
     */
    private $image;

    /**
     * Wrap a Vips\ImageClass around an underlying vips resource. 
     *
     * Don't call this yourself, users should stick to (for example)
     * Vips\ImageClass::newFromFile().
     *
     * @param resource $image The underlying vips image resource that this
     *  class should wrap.
     */
    public function __construct($image) 
    {
        $this->image = $image;
    }

    /* Apply a func to every numeric member of $mixed. Useful for self::subtract
     * etc.
     */ 
    private static function map_numeric($value, $func)
    {
        if (is_numeric($value)) {
            $value = $func($value);
        }
        else if (is_array($value)) {
            array_walk_recursive($value, function (&$item, $key) use ($func) {
                $item = map_numeric($item, $func);
            });
        }

        return $value;
    }

    /* Is a $value a rectangular 2D array?
     */
    static private function is_2D($value)
    {
        if (!is_array($value)) {
            return FALSE;
        }

        $height = count($value);
        if (!is_array($value[0])) {
            return FALSE;
        }
        $width = count($value[0]);

        foreach ($array as $row) {
            if (!is_array($row) ||
                count($row) != $width) { 
                return FALSE;
            }
        }

        return TRUE;
    }

    /* Turn a constant (eg. 1, "12", [1, 2, 3], [[1]]) into an image using
     * match_image as a guide.
     */
    private static function imageize($match_image, $value)
    {
        if (self::is_2D($value)) {
            $result = self::newFromArray($value);
        }
        else {
            $pixel = self::black(1, 1)->add($value)->cast($match_image->format);
            $result = $pixel->embed(0, 0, 
                $match_image->width, $match_image->height,
                ["extend" => "copy"]);
            $result->interpretation = $match_image->interpretation; 
        }

        return $result;
    }

    /* Unwrap an array of stuff ready to pass down to the vips_ layer. We
     * swap instances of the Vips\ImageClass for the plain resource.
     */
    private static function unwrap($result) 
    {
        array_walk_recursive($result, function (&$item, $key) {
            if ($item instanceof ImageClass) {
                $item = $item->image;
            }
        });

        return $result;
    }

    /* Is $value a VipsImage.
     */
    private static function is_image($value)
    {
            return is_resource($value) &&
                get_resource_type($value) == "GObject";
    }

    /* Wrap up the result of a vips_ call ready to erturn it to PHP. We do 
     * two things:
     *
     * - If the array is a singleton, we strip it off. For example, many 
     *   operations return a single result and there's no sense handling 
     *   this as an array of values, so we transform ["out" => x] -> x. 
     *
     * - Any VipsImage resources are rewrapped as instances of Vips\ImageClass.
     */
    private static function wrap($result) 
    {
        if (!is_array($result)) {
            $result = ["x" => $result];
        }

        array_walk_recursive($result, function (&$item) {
            if (self::is_image($item)) { 
                $item = new ImageClass($item);
            }
        });

        if (count($result) == 1) {
            $result = array_shift($result);
        }

        return $result;
    }

    /** 
     * Create a new Vips\ImageClass from a file on disc.
     *
     * @param string $filename The file to open.
     * @param array $options Any options to pass on to the load operation.
     *
     * @return A new Vips\ImageClass.
     */
    public static function newFromFile($filename, $options = []) 
    {
        $options = self::unwrap($options);
        $result = vips_image_new_from_file($filename, $options);
        return self::wrap($result);
    }

    /** 
     * Create a new Vips\ImageClass from a compressed image held as a string. 
     *
     * @param string $buffer The formatted image to open.
     * @param string $option_string Any text-style options to pass to the
     * selected loader. 
     * @param array $options Any options to pass on to the load operation.
     *
     * @return A new Vips\ImageClass.
     */
    public static function newFromBuffer($buffer, 
        $option_string = "", $options = []) 
    {
        $options = self::unwrap($options);
        $result = vips_image_new_from_buffer($buffer, $option_string, $options);
        return self::wrap($result);
    }

    /** 
     * Create a new Vips\ImageClass from a php array. 
     *
     * 2D arrays become 2D images. 1D arrays become 2D images with height 1. 
     *
     * @param array $array The array to make the image from. 
     * @param double $scale The "scale" metadata item. Useful for integer
     * convolution masks.
     * @param double $offset The "offset" metadata item. Useful for integer
     * convolution masks.
     *
     * @return A new Vips\ImageClass.
     */
    public static function newFromArray($array, $scale = 1, $offset = 0) 
    {
        $result = vips_image_new_from_array($array, $scale, $offset);
        return self::wrap($result);
    }

    /** 
     * Write an image to a file. 
     *
     * @param string $filename The file to write the image to.
     * @param array $options Any options to pass on to the selected save 
     * operation.
     *
     * @return bool TRUE for success and FALSE for failure.
     */
    public function writeToFile($filename, $options = []) 
    {
        $options = self::unwrap($options);
        $result = vips_image_write_to_file($this->image, $filename, $options);
        return self::wrap($result);
    }

    /** 
     * Write an image to a formatted string. 
     *
     * @param string $suffix The file type suffix, eg. ".jpg".
     * @param array $options Any options to pass on to the selected save 
     * operation.
     *
     * @return string The formatted image. 
     */
    public function writeToBuffer($suffix, $options = []) 
    {
        $options = self::unwrap($options);
        $result = vips_image_write_to_buffer($this->image, $suffix, $options);
        return self::wrap($result);
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
        $result = vips_image_get($this->image, $name);
        return self::wrap($result);
    }

    /**
     * Set any property on the underlying image.
     * 
     * @param string $name The property name. 
     * @param mixed $value The value to set for this property.
     *
     * @return void
     */
    public function __set($name, $value) 
    {
        vips_image_set($this->image, $name, $value);
    }

    /**
     * Call any vips operation.
     *
     * @param string $name The operation name. 
     * @param Vips\ImageClass $instance The instance this operation is being 
     * invoked from.
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
        $arguments = self::unwrap($arguments);
        $result = call_user_func_array("vips_call", $arguments);
        return self::wrap($result);
    }

    /* Handy for things like self::more. Call a 2-ary vips operator like
     * "more", but if the arg is not an image (ie. it's a constant), call
     * "more_const" instead.
     */
    private function call_enum($other, $base, $op, $options = [])
    {
        if ($other instanceof ImageClass) {
            return self::call($base, $this, 
                array_merge([$other, $op], $options));
        }
        else {
            return self::call($base . "_const", $this, 
                array_merge([$op, $other], $options));
        }
    }

    /**
     * Call any vips operation as an instance method.
     */
    public function __call($name, $arguments) 
    {
        return self::call($name, $this, $arguments); 
    }

    /**
     * Call any vips operation as a class method.
     */
    public static function __callStatic($name, $arguments) 
    {
        return self::call($name, NULL, $arguments); 
    }

    /* ArrayAccess interface ... we allow [] to get band.
     */

    public function offsetExists($offset) 
    {
        return $offset >= 0 && $offset <= $this->bands - 1;
    }

    public function offsetGet($offset) 
    {
        return self::offsetExists($offset) ? self::extract_band($offset) : NULL;
    }

    public function offsetSet($offset, $value) 
    {
        echo "Vips\ImageClass::offsetSet: not implemented\n"; 
    }

    public function offsetUnset($offset) 
    {
        echo "Vips\ImageClass::offsetUnset: not implemented\n"; 
    }

    /* add/sub/mul/div with a constant argument can use linear for a huge
     * speedup.
     */

    /**
     * Add to this image.
     *
     * @param int|double|int[]|double[]|Vips\ImageClass $other The thing to 
     * add to this image.
     * @param array $options Any options to pass on to the add operation.
     *
     * @return Vips\ImageClass A new image.
     */
    public function add($other, $options = [])
    {
        if ($other instanceof ImageClass) {
            return self::call("add", $this, array_merge([$other], $options));
        }
        else {
            return self::linear(1, $other, $options); 
        }
    }

    public function subtract($other, $options = [])
    {
        if ($other instanceof ImageClass) {
            return self::call("subtract", $this, 
                array_merge([$other], $options));
        }
        else {
            $other = map_numeric($other, function ($value) {
                return -1 * $value;
            });
            return self::linear($this, 1, $other, $options);
        }
    }

    public function multiply($other, $options = [])
    {
        if ($other instanceof ImageClass) {
            return self::call("multiply", $this, 
                array_merge([$other], $options));
        }
        else {
            return self::linear($this, $other, 0, $options);
        }
    }

    public function divide($other, $options = [])
    {
        if ($other instanceof ImageClass) {
            return self::call("divide", $this, array_merge([$other], $options));
        }
        else {
            $other = map_numeric($other, function ($value) {
                return $value ** -1;
            });
            return self::linear($this, $other, 0, $options);
        }
    }

    public function remainder($other, $options = [])
    {
        if ($other instanceof ImageClass) {
            return self::call("remainder", $this, 
                array_merge([$other], $options));
        }
        else {
            return self::call("remainder_const", $this, 
                array_merge([$other], $options));
        }
    }

    public function pow($other, $options = [])
    {
        return self::call_enum($other, "math2", "pow", $options);
    }

    public function wop($other, $options = [])
    {
        return self::call_enum($other, "math2", "wop", $options);
    }

    public function lshift($other, $options = [])
    {
        return self::call_enum($other, "boolean", "lshift", $options);
    }

    public function rshift($other, $options = [])
    {
        return self::call_enum($other, "boolean", "rshift", $options);
    }

    public function and($other, $options = [])
    {
        return self::call_enum($other, "boolean", "and", $options);
    }

    public function or($other, $options = [])
    {
        return self::call_enum($other, "boolean", "or", $options);
    }

    public function eor($other, $options = [])
    {
        return self::call_enum($other, "boolean", "eor", $options);
    }

    public function more($other, $options = [])
    {
        return self::call_enum($other, "relational", "more", $options);
    }

    public function moreEq($other, $options = [])
    {
        return self::call_enum($other, "relational", "moreeq", $options);
    }

    public function less($other, $options = [])
    {
        return self::call_enum($other, "relational", "less", $options);
    }

    public function lessEq($other, $options = [])
    {
        return self::call_enum($other, "relational", "lesseq", $options);
    }

    public function equal($other, $options = [])
    {
        return self::call_enum($other, "relational", "equal", $options);
    }

    public function notEq($other, $options = [])
    {
        return self::call_enum($other, "relational", "noteq", $options);
    }

    /* bandjoin can use bandjoin_const if all of $other are numeric. 
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
        $is_const = TRUE;
        foreach ($other as $item) {
            if (!is_numeric($item)) {
                $is_const = FALSE;
                break;
            }
        }

        /* We can't use self::bandjoin(), that would just recurse.
         */
        if ($is_const) {
            return self::call("bandjoin_const", $this, 
                array_merge([$other], $options));
        }
        else {
            return self::call("bandjoin", NULL, 
                array_merge([array_merge([$this], $other)], $options));
        }
    }

    public function bandsplit($options = [])
    {
        $result = [];

        for ($i = 0; $i < $this->bands; $i++) {
            $result[] = $this->extract_band($i. $options);
        }

        return $result;
    }

    /* bandrank will appear as a static class member, as 
     * Vips\ImageClass::bandrank([a, b, c]), but it's better as an instance 
     * method.
     * 
     * We need to define this by hand.
     */
    public function bandrank($other, $options = [])
    {
        /* Allow a single unarrayed value as well.
         */
        if (!is_array($other)) {
            $other = [$other];
        }

        return self::call("bandrank", NULL, 
            array_merge([array_merge([$this], $other)], $options));
    }

    /* Position of max is awkward with plain self::max.
     */
    public function maxpos()
    {
        $result = $this->max(["x" => TRUE, "y" => TRUE]);
        $out = $result["out"];
        $x = $result["x"];
        $y = $result["y"];

        return [$out, $x, $y];
    }

    /* Position of min is awkward with plain self::min.
     */
    public function minpos()
    {
        $result = $this->min(["x" => TRUE, "y" => TRUE]);
        $out = $result["out"];
        $x = $result["x"];
        $y = $result["y"];

        return [$out, $x, $y];
    }

    /* We need different imageize rules for this. We need $then and $else to
     * match each other first, and only if they are both constants do we
     * match to $this.
     */
    public function ifthenelse($then, $else, $options = [])
    {
        $match_image = NULL;
        foreach ([$then, $else, $this] as $item) {
            if ($item instanceof ImageClass) {
                $match_image = $item;
                break;
            }
        }

        if (!($then instanceof ImageClass)) {
            $then = self::imageize($match_image, $then);
        }

        if (!($else instanceof ImageClass)) {
            $else = self::imageize($match_image, $else);
        }

        return self::call("ifthenelse", $this, 
            array_merge([$then, $else], $options));
    }

    # Return the largest integral value not greater than the argument.
    public function floor() 
    {
        return $this->round("floor");
    }

    # Return the smallest integral value not less than the argument.
    public function ceil() 
    {
        return $this->round("ceil");
    }

    # Return the nearest integral value.
    public function rint() 
    {
        return $this->round("rint");
    }

    # AND image bands together.
    public function bandand() 
    {
        return $this->bandbool("and");
    }

    # OR image bands together.
    public function bandor() 
    {
        return $this->bandbool("or");
    }

    # EOR image bands together.
    public function bandeor() 
    {
        return $this->bandbool("eor");
    }

    # Return the real part of a complex image.
    public function real() 
    {
        return $this->complexget("real");
    }

    # Return the imaginary part of a complex image.
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

    # Return an image converted to polar coordinates.
    public function polar() 
    {
        return $this->complex("polar");
    }

    # Return an image converted to rectangular coordinates.
    public function rect() 
    {
        return $this->complex("rect");
    }

    # Return the complex conjugate of an image.
    public function conj() 
    {
        return $this->complex("conj");
    }

    # Return the sine of an image in degrees.
    public function sin() 
    {
        return $this->math("sin");
    }

    # Return the cosine of an image in degrees.
    public function cos() 
    {
        return $this->math("cos");
    }

    # Return the tangent of an image in degrees.
    public function tan() 
    {
        return $this->math("tan");
    }

    # Return the inverse sine of an image in degrees.
    public function asin() 
    {
        return $this->math("asin");
    }

    # Return the inverse cosine of an image in degrees.
    public function acos() 
    {
        return $this->math("acos");
    }

    # Return the inverse tangent of an image in degrees.
    public function atan() 
    {
        return $this->math("atan");
    }

    # Return the natural log of an image.
    public function log() 
    {
        return $this->math("log");
    }

    # Return the log base 10 of an image.
    public function log10() 
    {
        return $this->math("log10");
    }

    # Return e ** pixel.
    public function exp() 
    {
        return $this->math("exp");
    }

    # Return 10 ** pixel.
    public function exp10() 
    {
        return $this->math("exp10");
    }

    # Erode with a structuring element.
    public function erode($mask) 
    {
        return $this->morph($mask, "erode");
    }

    # Dilate with a structuring element.
    public function dilate($mask) 
    {
        return $this->morph($mask, "dilate");
    }

    # size x size median filter.
    public function median($size) 
    {
        return $this->rank(size, size, (size * size) / 2);
    }

    # Flip horizontally.
    public function fliphor() 
    {
        return $this->flip("horizontal");
    }

    # Flip vertically.
    public function flipver() 
    {
        return $this->flip("vertical");
    }

    # Rotate 90 degrees clockwise.
    public function rot90() 
    {
        return $this->rot("d90");
    }

    # Rotate 180 degrees.
    public function rot180() 
    {
        return $this->rot("d180");
    }

    # Rotate 270 degrees clockwise.
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
