--TEST--
Vips\Image::maxpos works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $image = Vips\Image::new_from_array([[1, 2, 3], [4, 5, 6]]);

  $result = $image->maxpos();

  if ($result[0] == 6 &&
	$result[1] == 2 &&
	$result[2] == 1 ) {
	echo "pass";
  }
?>
--EXPECT--
pass
