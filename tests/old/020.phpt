--TEST--
Vips\Image::newFromBuffer works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $buffer = file_get_contents($filename);

  $image = Vips\Image::newFromBuffer($buffer, "", ["shrink" => 2]);
  if ($image->width == 800) {
  	echo "pass\n";
  }
?>
--EXPECT--
pass
