--TEST--
VImage::ifthenelse(const, const) works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = VImage::new_from_file($filename);

  $image = $image->more(34)->ifthenelse(128, 255);

  $pixel = $image->getpoint(0, 0);

  if ($pixel == [128, 128, 255]) { 
	echo "pass";
  }
?>
--EXPECT--
pass
