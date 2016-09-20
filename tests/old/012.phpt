--TEST--
new_from_buffer works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $buffer = file_get_contents($filename);

  $image = vips_image_new_from_buffer($buffer)["out"];
  $width1 = vips_image_get($image, "width");

  $image = vips_image_new_from_buffer($buffer, "shrink=2")["out"];
  $width2 = vips_image_get($image, "width");

  $image = vips_image_new_from_buffer($buffer, "", ["shrink" => 4])["out"];
  $width3 = vips_image_get($image, "width");

  if ($width1 == 1600 &&
	$width2 = 800 &&
	$width4 = 400) { 
	echo "pass";
  }
?>
--EXPECT--
pass
