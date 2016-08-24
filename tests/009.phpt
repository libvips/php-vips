--TEST--
array image args work
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/IMG_0073.JPG";
  $image = vips_image_new_from_file($filename)["out"];

  $r = vips_call("extract_band", $image, 0)["out"];
  $g = vips_call("extract_band", $image, 1)["out"];
  $b = vips_call("extract_band", $image, 2)["out"];
  $new_image = vips_call("bandjoin", [$r, $g, $b])["out"];

  $diff = vips_call("subtract", $image, $new_image)["out"];
  $diff = vips_call("abs", $diff)["out"];
  $diff = vips_call("max", $diff)["out"];

  if ($diff == 0) {
	echo "pass";
  }
?>
--EXPECT--
pass
