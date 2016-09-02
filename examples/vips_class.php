#!/usr/bin/env php
<?php
	include '../vips.php';

	$image = Vips\Image::newFromFile($argv[1]); 

	echo "width = ", $image->width, "\n";

	$image = $image->invert();

	$image->writeToFile($argv[2]);
?>
