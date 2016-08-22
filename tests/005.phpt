--TEST--
Check we can call any vips operation 
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/IMG_0073.JPG";
  $output_filename = dirname(__FILE__) . "/x.tif";
  $image = vips_image_new_from_file($filename);
  $result = vips_php_call("invert", $image);
  $image = $result["out"];
  vips_image_write_to_file($image, $output_filename);
  $new_image = vips_image_new_from_file($output_filename);
  if ($new_image != FALSE) {
    echo("pass\n");
  }
?>
--EXPECT--
pass
--CLEAN--
<?php
  $output_filename = dirname(__FILE__) . "/x.tif";
  unlink($output_filename);
?>

