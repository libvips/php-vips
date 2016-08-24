--TEST--
vips can get image header fields 
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/IMG_0073.JPG";
  $image = vips_image_new_from_file($filename)["out"];
  $width = vips_image_get($image, "width");
  if ($width == 5472) {
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

