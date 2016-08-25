<?php

/* This file tries to make a nice API over the raw `vips_call` thing defined in C.
 */

if (!extension_loaded("vips")) {
	dl('vips.' . PHP_SHLIB_SUFFIX);
}

class VImage {
	/* The resource for the underlying VipsImage.
	 */
	private $image;

	function __construct($image) {
		$this->image = $image;
	}

	public static function new_from_file($filename, $options = []) {
		$image = vips_image_new_from_file($filename, $options)["out"];
		return new Vimage($image);
	}

	public static function new_from_buffer($buffer, $option_string = "", $options = []) {
		$image = vips_image_new_from_buffer($buffer, $option_string, $options)["out"];
		return new Vimage($image);
	}

	public static function new_from_array($array, $scale = 1, $offset = 0) {
		$image = vips_image_new_from_array($array, $scale, $offset);
		return new Vimage($image);
	}

	public function write_to_file($filename, $options = []) {
		return vips_image_write_to_file($this->image, $filename, $options);
	}

	public function write_to_buffer($suffix, $options = []) {
		return vips_image_write_to_buffer($this->image, $suffix, $options)["buffer"];
	}

	public function __get($name) {
		return vips_image_get($this->image, $name);
	}

	/* Works for invert, won't work for many others, need to revise this.
	 */
	public function __call($name, $arguments) {
		$x = array_merge([$name, $this->image], $arguments);
		$image = call_user_func_array("vips_call", $x)["out"];
		return new Vimage($image);
	}
}
?>
