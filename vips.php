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

	public function __call($name, $arguments) 
	{
		$arguments = array_merge([$name, $this], $arguments);
		$arguments = self::unwrap($arguments);
		$result = call_user_func_array("vips_call", $arguments);
		return self::wrap($result);
	}

	public static function __callStatic($name, $arguments) 
	{
		$arguments = array_merge([$name, NULL], $arguments);
		$arguments = self::unwrap($arguments);
		$result = call_user_func_array("vips_call", $arguments);
		return self::wrap($result);
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
