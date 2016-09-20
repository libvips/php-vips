--TEST--
new_from_array has optional scale and offset
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $image = vips_image_new_from_array([1, 2, 3]);
  $scale1 = vips_image_get($image, "scale");
  $offset1 = vips_image_get($image, "offset");

  $image = vips_image_new_from_array([1, 2, 3], 8);
  $scale2 = vips_image_get($image, "scale");
  $offset2 = vips_image_get($image, "offset");

  $image = vips_image_new_from_array([1, 2, 3], 8, 12);
  $scale3 = vips_image_get($image, "scale");
  $offset3 = vips_image_get($image, "offset");

  if ($scale1 == 1 &&
	$offset1 == 0 &&
    $scale2 == 8 &&
	$offset2 == 0 &&
    $scale3 == 8 &&
	$offset3 == 12) {
	echo "pass";
  }
?>
--EXPECT--
pass
