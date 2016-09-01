#!/usr/bin/env php
<?php
dl('vips.' . PHP_SHLIB_SUFFIX);

$image = vips_image_new_from_array([[1, 2, 3], [4, 5, 6]]);
$rgb = vips_call("bandjoin", NULL, [$image, $image, $image])["out"];

# multiply R by 2
$rgb = vips_call("linear", $rgb, 2, 0)["out"];

vips_image_write_to_file($rgb, "x.v");
?>
