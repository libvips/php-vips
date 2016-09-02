--TEST--
can import Vips\Image class
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $image = Vips\Image::new_from_array([1, 2, 3]);
?>
--EXPECT--
