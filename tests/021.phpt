--TEST--
Vips\Image::new_from_array works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $image = Vips\Image::new_from_array([1, 2, 3], 8, 12);

  if ($image->width == 3 &&
	$image->height == 1 &&
	$image->scale == 8 &&
	$image->offset == 12) {
	echo "pass";
  }
?>
--EXPECT--
pass
