--TEST--
can use 2D array as image
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = vips_image_new_from_file($filename)["out"];

  # this will barf horribly if the array constant is not turned into an image
  $sharp = vips_call("conv", $image, [[-1, -1, -1], [-1, 9, -1], [-1, -1, -1]])["out"];
?>
--EXPECT--
