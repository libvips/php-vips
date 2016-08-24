--TEST--
new_from_file supports optional args
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = vips_image_new_from_file($filename, 
	array("shrink" => 8))["out"];
  $width = vips_image_get($image, "width");
  if ($width == 200) {
	echo "pass";
  }
?>
--EXPECT--
pass
