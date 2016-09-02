--TEST--
Vips\Image::bandand works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";
  $image = Vips\Image::new_from_file($filename);

  $pass = TRUE;

  /* The original value of pixel (0, 0).
   */
  $pixel = [39.1, 39.5, 34.9];

  $true = $pixel[0] & $pixel[1] & $pixel[2];
  if ($image->bandand()->getpoint(0, 0) != [$true]) { 
  	$pass = FALSE;
  }

  if ($pass) {
	echo "pass";
  }
?>
--EXPECT--
pass
