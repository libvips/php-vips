--TEST--
VImage::new_from_file works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = VImage::new_from_file($filename, ["shrink" => 2]);
  if ($image->width == 800) {
  	echo "pass\n";
  }
?>
--EXPECT--
pass
