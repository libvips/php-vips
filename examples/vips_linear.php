#!/usr/bin/env php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	$image = vips_call("black", 100, 100, array("bands" => 3))["out"];
	$image = vips_call("linear", $image, [1, 1, 1], [255, 0, 0])["out"];
	vips_image_write_to_file($image, $argv[1]);
?>
