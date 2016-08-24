--TEST--
input array double args work
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $image = vips_call("black", 100, 100, array("bands" => 3))["out"];
  $image = vips_call("linear", $image, [1, 1, 1], [255, 128, 0])["out"];
  $pixel = vips_call("crop", $image, 10, 10, 1, 1)["out"];
  $r = vips_call("extract_band", $pixel, 0)["out"];
  $r = vips_call("avg", $r)["out"];
  $g = vips_call("extract_band", $pixel, 1)["out"];
  $g = vips_call("avg", $g)["out"];
  $b = vips_call("extract_band", $pixel, 2)["out"];
  $b = vips_call("avg", $b)["out"];

  if ($r == 255 &&
	$g == 128 &&
	$b == 0) {
	echo "pass";
  }
?>
--EXPECT--
pass
