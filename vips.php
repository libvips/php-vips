<?php

/* This file tries to make a nice API over the raw `vips_call` thing defined 
 * in C.
 */

if (!extension_loaded("vips")) {
	dl('vips.' . PHP_SHLIB_SUFFIX);
}

class VImage 
{
	/* The resource for the underlying VipsImage.
	 */
	private $image;

	function __construct($image) 
	{
		$this->image = $image;
	}

	/* Unwrap an array of stuff ready to pass down to the vips_ layer. We
	 * swap instances of the VImage class for the plain resource.
	 */
	private static function unwrap($result) 
	{
		array_walk_recursive($result, function (&$item, $key) {
			if ($item instanceof VImage) {
				$item = $item->image;
			}
		});

		return $result;
	}

	/* Wrap up the result of a vips_ call ready to erturn it to PHP. We do 
	 * two things:
	 *
	 * - If the array is a singleton, we strip it off. For example, many 
	 *   operations return a single result and there's no sense handling 
	 *   this as an array of values, so we transform ["out" => x] -> x. 
	 *
	 * - Any VipsImage resources are rewrapped as instances of our VImage
	 *   class. 
	 */
	private static function wrap($result) 
	{
		if (!is_array($result)) {
			$result = ["x" => $result];
		}

		array_walk_recursive($result, function (&$item) {
			if (is_resource($item) &&
				get_resource_type($item) == "GObject") {
				$item = new VImage($item);
			}
		});

		if (count($result) == 1) {
			$result = array_shift($result);
		}

		return $result;
	}

	public static function new_from_file($filename, $options = []) 
	{
		$options = self::unwrap($options);
		$result = vips_image_new_from_file($filename, $options);
		return self::wrap($result);
	}

	public static function new_from_buffer($buffer, 
		$option_string = "", $options = []) 
	{
		$options = self::unwrap($options);
		$result = vips_image_new_from_buffer($buffer, $option_string, $options);
		return self::wrap($result);
	}

	public static function new_from_array($array, $scale = 1, $offset = 0) 
	{
		$result = vips_image_new_from_array($array, $scale, $offset);
		return self::wrap($result);
	}

	public function write_to_file($filename, $options = []) 
	{
		$options = self::unwrap($options);
		$result = vips_image_write_to_file($this->image, $filename, $options);
		return self::wrap($result);
	}

	public function write_to_buffer($suffix, $options = []) 
	{
		$options = self::unwrap($options);
		$result = vips_image_write_to_buffer($this->image, $suffix, $options);
		return self::wrap($result);
	}

	public function __get($name) 
	{
		$result = vips_image_get($this->image, $name);
		return self::wrap($result);
	}

	public static function call($name, $instance, $arguments) 
	{
		$arguments = array_merge([$name, $instance], $arguments);
		$arguments = self::unwrap($arguments);
		$result = call_user_func_array("vips_call", $arguments);
		return self::wrap($result);
	}

	public function __call($name, $arguments) 
	{
		return self::call($name, $this, $arguments); 
	}

	public static function __callStatic($name, $arguments) 
	{
		return self::call($name, NULL, $arguments); 
	}

	/* bandjoin will appear as a static class member, as 
	 * VImage::bandjoin([a, b, c]), but it's handy to have as  method as well.
	 * We need to define this by hand.
	 */
	public function bandjoin($other, $options = [])
	{
			/* Allow a single unarrayed value as well.
			 */
			if (!is_array($other)) {
				$other = [$other];
			}

			/* If $other is all numbers, we can use VImage::bandjoin_const().
			 */
			$is_const = TRUE;
			foreach ($other as $item) {
				if (!is_numeric($item)) {
					$is_const = FALSE;
					break;
				}
			}

			if ($is_const) {
				return VImage::bandjoin_const($this, $other, $options);
			}
			else {
				return VImage::bandjoin([$this] + $other, $options);
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
	 * VImage::bandrank([a, b, c]), but it's handy to have as  method as well.
	 * We need to define this by hand.
	 */
	public function bandrank($other, $options = [])
	{
			/* Allow a single unarrayed value as well.
			 */
			if (!is_array($other)) {
				$other = [$other];
			}

			return VImage::bandrank([$this] + $other, $options);
	}

	/* Position of max is awkward with plain VImage::max.
	 */
	public function maxpos()
	{
			$result = $this->max(["x" => TRUE, "y" => TRUE]);
			$out = $result["out"];
			$x = $result["x"];
			$y = $result["y"];

			return [$out, $x, $y];
	}

	/* Position of min is awkward with plain VImage::min.
	 */
	public function minpos()
	{
			$result = $this->min(["x" => TRUE, "y" => TRUE]);
			$out = $result["out"];
			$x = $result["x"];
			$y = $result["y"];

			return [$out, $x, $y];
	}

	/* paste py stuff in here as a reminder

	# we need different constant expansion rules for this operator ... we need 
	# to imageize th and el to match each other first
    @add_doc(generate_docstring("ifthenelse"))
    def ifthenelse(self, th, el, **kwargs):
        for match_image in [th, el, self]:
            if isinstance(match_image, Vips.Image):
                break

        if not isinstance(th, Vips.Image):
            th = imageize(match_image, th)
        if not isinstance(el, Vips.Image):
            el = imageize(match_image, el)

        return _call_base("ifthenelse", [th, el], kwargs, self)
	 */

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
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */

?>
