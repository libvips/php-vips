--TEST--
__set and __get work
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = Vips\Image::new_from_file($filename);

  $image->poop = "banana";
  $value = $image->poop;

  if ($value == "banana") {
	echo "pass";
  }
?>
--EXPECT--
pass
