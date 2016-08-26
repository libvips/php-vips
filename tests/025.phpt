--TEST--
VImage::__callStatic works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $image = VImage::black(1, 2, ["bands" => 3]);

  if ($image->width == 1 &&
	$image->height == 2 &&
	$image->bands == 3) { 
	echo "pass";
  }
?>
--EXPECT--
pass
