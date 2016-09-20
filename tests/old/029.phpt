--TEST--
Vips\Image::add(const) works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
  $image = $image->add(1);

  $pixel = $image->crop(0, 0, 1, 1);
  $pixel = $pixel->avg();

  if ($pixel == 2) { 
	echo "pass";
  }
?>
--EXPECT--
pass
