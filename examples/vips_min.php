#!/usr/bin/env php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	$point = vips_call("black", NULL, 1, 1)["out"];
	$image = vips_call("embed", $point, 10, 20, 100, 100, 
		array("extend" => "white"))["out"];

	$result = vips_call("min", $image, 
		array("x" => true, "y" => true, "x_array" => true));
	echo "out = ";
	var_dump($result);
	$mn = $result["out"];
	$x = $result["x"];
	$y = $result["y"];
	$x_array = $result["x_array"];

	echo "min = ", $mn, "\n";
	echo "x = ", $x, "\n";
	echo "y = ", $y, "\n";
	echo "x_array = [";
	for ($i = 0; $i < count($x_array); $i++) {
		if ($i > 0) {
			echo ", ";
		}
		echo $x_array[$i];
	}
	echo "]\n";
?>
