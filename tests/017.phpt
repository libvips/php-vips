--TEST--
can set enum from int
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $point = vips_call("black", NULL, 1, 1)["out"];
  $image = vips_call("embed", $point, 10, 20, 100, 100, 
		array("extend" => 4))["out"];

  $result = vips_call("min", $image, 
		array("x" => true, "y" => true, "x_array" => true));
  $x_array = $result["x_array"];

  if ($x_array == [10]) {
	echo "pass";
  }
?>
--EXPECT--
pass
