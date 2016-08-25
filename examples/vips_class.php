#!/usr/bin/env php
<?php
	include '../vips.php';

	$image = VImage::new_from_file($argv[1]); 

	echo "width = ", $image->width, "\n";

	$image = $image->invert();

	$image->write_to_file($argv[2]);
?>
