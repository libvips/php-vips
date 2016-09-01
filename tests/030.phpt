--TEST--
VImage::getpoint() works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $image = VImage::new_from_array([[1, 2, 3], [4, 5, 6]]);
  $rgb = $image->bandjoin([$image, $image]);
  $rgb = $rgb->add(1);

  $pixel = $rgb->getpoint(0, 0);

  if ($pixel == [2, 2, 2]) { 
	echo "pass";
  }
?>
--EXPECT--
pass
