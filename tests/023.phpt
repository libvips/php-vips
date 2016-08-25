--TEST--
VImage::write_to_buffer works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $output_filename = dirname(__FILE__) . "/x.jpg";

  $image = VImage::new_from_file($filename);

  $buffer1 = $image->write_to_buffer(".jpg");

  $image->write_to_file($output_filename);
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

