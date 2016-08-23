#!/usr/bin/env php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	$x = vips_image_new_from_file($argv[1]);

	$result = vips_call("invert", $x);
	$x = $result["out"];

	vips_image_write_to_file($x, $argv[2]);
?>
