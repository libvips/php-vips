<?php 
  include 'vips.php';

  $image = VImage::new_from_array([[1, 2, 3], [4, 5, 6]]);
  $image = $image->add(1);

  $pixel = $image->crop(0, 0, 1, 1);
  $pixel = $pixel->avg();

  if ($pixel == 2) { 
	echo "pass";
  }
?>
