--TEST--
Vips\Image::bandjoin works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $image = Vips\Image::new_from_array([[1, 2, 3], [4, 5, 6]]);
  $rgb = $image->bandjoin([$image, $image]);

  if ($rgb->bands == 3 &&
	$rgb->bands == 3) {
	echo "pass";
  }
?>
--EXPECT--
pass
