#!/usr/bin/env php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	$x = vips_image_new_from_file($argv[1])["out"];
	$profile = vips_image_get($x, "icc-profile-data");

	echo $argv[1], " profile:\n";
	var_dump($profile);

?>
