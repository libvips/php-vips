--TEST--
write_to_buffer works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = vips_image_new_from_file($filename)["out"];
  $output_filename = dirname(__FILE__) . "/x.jpg";

  $buffer1 = vips_image_write_to_buffer($image, ".jpg")["buffer"];

  vips_image_write_to_file($image, $output_filename);
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

