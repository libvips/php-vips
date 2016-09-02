--TEST--
Vips\Image::ifthenelse(const, image) works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = Vips\Image::new_from_file($filename);

  $image = $image->more(34)->ifthenelse($image, 255);

  $pixel = $image->getpoint(0, 0);

  if ($pixel == [39, 38, 255]) { 
	echo "pass";
  }
?>
--EXPECT--
pass
