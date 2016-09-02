--TEST--
Vips\Image::newFromFile works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = Vips\Image::newFromFile($filename, ["shrink" => 2]);
  if ($image->width == 800) {
  	echo "pass\n";
  }
?>
--EXPECT--
pass
