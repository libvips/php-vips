--TEST--
VImage::__call works
--SKIPIF--
<?php if (!extension_loaded("vips")) print "skip"; ?>
--FILE--
<?php 
  include 'vips.php';

  $filename = dirname(__FILE__) . "/images/img_0076.jpg";

  $image = VImage::new_from_file($filename);
  $image = $image->embed(10, 20, 3000, 2000, ["extend" => "copy"]);

  if ($image->width == 3000) {
	echo "pass";
  }
?>
--EXPECT--
pass
