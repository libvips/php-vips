--TEST--
VImage::write_to_file works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $output_filename = dirname(__FILE__) . "/x.tif";

  $image = VImage::new_from_file($filename);
  $image->write_to_file($output_filename);
  $image = VImage::new_from_file($output_filename);

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

