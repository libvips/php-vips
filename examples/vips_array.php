#!/usr/bin/env php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	$array = vips_image_new_from_array([1, 2, 3]);

	vips_image_write_to_file($array ,$argv[1]);
?>
