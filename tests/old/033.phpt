--TEST--
Vips\Image::ifthenelse(image, const) works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = Vips\Image::newFromFile($filename);

  $image = $image->more(34)->ifthenelse(255, $image);

  $pixel = $image->getpoint(0, 0);

  if ($pixel == [255, 255, 34]) {
	echo "pass";
  }
?>
--EXPECT--
pass
