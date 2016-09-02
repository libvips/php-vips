--TEST--
Vips\Image::writeToFile works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $output_filename = dirname(__FILE__) . "/x.tif";

  $image = Vips\Image::newFromFile($filename);
  $image->writeToFile($output_filename);
  $image = Vips\Image::newFromFile($output_filename);

  if ($image->width == 1600) {
	echo "pass";
  }
?>
--EXPECT--
pass
--CLEAN--
<?php
  $output_filename = dirname(__FILE__) . "/x.tif";
  unlink($output_filename);
?>

