#!/usr/bin/env php
<?php

include '../src/Image.php';

use Vips\Image\Image;

$im = Image::newFromFile($argv[1], ["access" => "sequential"]);

$im = $im->crop(100, 100, $im->width - 200, $im->height - 200);

$im = $im->reduce(1.0 / 0.9, 1.0 / 0.9, ["kernel" => "linear"]);

$mask = Image::newFromArray(
		  [[-1,  -1, -1], 
		   [-1,  16, -1], 
		   [-1,  -1, -1]], 8);
$im = $im->conv($mask);

$im->writeToFile($argv[2]);

?>
