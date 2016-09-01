--TEST--
VImage::ifthenelse(image, const) works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = VImage::new_from_file($filename);

  $image = $image->more(34)->ifthenelse(255, $image);

  $pixel = $image->getpoint(0, 0);

  if ($pixel == [255, 255, 34]) {
	echo "pass";
  }
?>
--EXPECT--
pass
