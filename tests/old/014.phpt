--TEST--
new_from_array works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $image = vips_image_new_from_array([1, 2, 3]);
  $width1 = vips_image_get($image, "width");
  $height1 = vips_image_get($image, "height");

  $image = vips_image_new_from_array([[1, 2, 3], [4, 5, 6]]);
  $width2 = vips_image_get($image, "width");
  $height2 = vips_image_get($image, "height");

  if ($width1 == 3 &&
	$height1 == 1 &&
	$width2 = 3 &&
	$height2 == 2) {
	echo "pass";
  }
?>
--EXPECT--
pass
