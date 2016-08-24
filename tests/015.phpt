--TEST--
new_from_array sets values correctly
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $image = vips_image_new_from_array([[1, 2, 3], [4, 5, 6]]);

  $pixel = vips_call("crop", $image, 2, 1, 1, 1)["out"];
  $value = vips_call("avg", $pixel)["out"];

  if ($value == 6) {
	echo "pass";
  }
?>
--EXPECT--
pass
