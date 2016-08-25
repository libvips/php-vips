--TEST--
vips_call supports optional input and output args
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $point = vips_call("black", NULL, 1, 1)["out"];
  $image = vips_call("embed", $point, 10, 20, 100, 100, 
	array("extend" => "white"))["out"];

  $result = vips_call("min", $image, array("x" => true, "y" => true));

  if ($result["out"] == 0 &&
    $result["x"] == 10 &&
    $result["y"] == 20) {
    echo("pass\n");
  }
?>
--EXPECT--
pass
