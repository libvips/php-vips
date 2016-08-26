--TEST--
can use 1D array as constant image
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $image = vips_image_new_from_array([[1, 2, 3], [4, 5, 6]]);
  $rgb = vips_call("bandjoin", NULL, [$image, $image, $image])["out"];

  # multiply R by 2
  $rgb = vips_call("linear", $rgb, [2, 1, 1], [0, 0, 0])["out"];

  $pixel = vips_call("crop", $rgb, 0, 0, 1, 1)["out"];
  $r = vips_call("extract_band", $pixel, 0)["out"];
  $r = vips_call("avg", $r)["out"];
  $g = vips_call("extract_band", $pixel, 1)["out"];
  $g = vips_call("avg", $g)["out"];
  $b = vips_call("extract_band", $pixel, 2)["out"];
  $b = vips_call("avg", $b)["out"];

  if ($r == 2 &&
    $g == 1 &&
    $b == 1) {
    echo "pass";
  }
?>
--EXPECT--
pass
