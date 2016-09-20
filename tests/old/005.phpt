--TEST--
we can call any vips operation 
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $output_filename = dirname(__FILE__) . "/x.tif";
  $image = vips_image_new_from_file($filename)["out"];
  $result = vips_call("invert", $image);
  $image = $result["out"];
  vips_image_write_to_file($image, $output_filename);
  $new_image = vips_image_new_from_file($output_filename)["out"];
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

