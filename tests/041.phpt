--TEST--
Vips\Image::[] works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = Vips\Image::new_from_file($filename);

  $image = $image->invert()[1];

  if ($image->bands == 1) {
	echo "pass";
  }
?>
--EXPECT--
pass
