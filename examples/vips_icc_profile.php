#!/usr/bin/env php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	$x = vips_image_new_from_file($argv[1])["out"];
	$type = vips_image_get_typeof($x, "icc-profile-data");

	if ($type > 0) {
		$profile = vips_image_get($x, "icc-profile-data");
		echo $argv[1], " profile, ", strlen($profile), " bytes of data\n";
	}
	else {
		echo $argv[1], " has no profile\n";
	}
?>
