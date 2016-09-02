--TEST--
Vips\Image::bandrank() works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = Vips\Image::new_from_file($filename);

  $image = $image->bandrank($image);

  if ($image->getpoint(0, 0) == [39, 38, 34]) {
	echo "pass";
  }
?>
--EXPECT--
pass
