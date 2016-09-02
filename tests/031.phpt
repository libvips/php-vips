--TEST--
Vips\Image::bandjoin(const) works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $image = Vips\Image::new_from_array([[1, 2, 3], [4, 5, 6]]);
  $imagea = $image->bandjoin(255);

  $pixel = $imagea->getpoint(0, 0);

  if ($pixel == [1, 255]) { 
	echo "pass";
  }
?>
--EXPECT--
pass
