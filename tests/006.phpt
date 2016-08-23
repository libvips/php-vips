--TEST--
Check we support optional output args
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/IMG_0073.JPG";
  $output_filename = dirname(__FILE__) . "/x.tif";
  $image = vips_image_new_from_file($filename);
  $result = vips_call("max", $image, array("x" => true, "y" => true));
  if ($result["out"] == 255 &&
    $result["x"] == 2893 &&
    $result["y"] == 1494) {
    echo("pass\n");
  }
?>
--EXPECT--
pass
