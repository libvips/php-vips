--TEST--
Vips\Image::writeToBuffer works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $output_filename = dirname(__FILE__) . "/x.jpg";

  $image = Vips\Image::newFromFile($filename);

  $buffer1 = $image->writeToBuffer(".jpg");

  $image->writeToFile($output_filename);
  $buffer2 = file_get_contents($output_filename);

  if ($buffer1 == $buffer2) {
	echo "pass";
  }
?>
--EXPECT--
pass
--CLEAN--
<?php
  $output_filename = dirname(__FILE__) . "/x.jpg";
  unlink($output_filename);
?>

