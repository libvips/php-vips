#!/usr/bin/env php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	$x = vips_image_new_from_file($argv[1])["out"];
	vips_image_write_to_file($x, $argv[2]);
?>
