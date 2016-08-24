--TEST--
typeof works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = vips_image_new_from_file($filename)["out"];

  $profile_type = vips_image_get_typeof($image, "icc-profile-data");
  $exif_type = vips_image_get_typeof($image, "exif-data");

  if ($profile_type == 0 &&
	$exif_type != 0) { 
	echo "pass";
  }
?>
--EXPECT--
pass
