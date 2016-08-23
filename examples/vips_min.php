#!/usr/bin/env php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	$point = vips_call("black", 1, 1)["out"];
	$image = vips_call("embed", $point, 10, 20, 100, 100, 
		array("extend" => "white"))["out"];

	$result = vips_call("min", $image, array("x" => true, "y" => true));
	var_dump($result);
	$mn = $result["out"];
	$x = $result["x"];
	$y = $result["y"];

	echo "min = ", $mn, "\n";
	echo "x = ", $x, "\n";
	echo "y = ", $y, "\n";
?>
