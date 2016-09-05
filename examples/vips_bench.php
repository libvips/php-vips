#!/usr/bin/env php
<?php

include '../src/ImageClass.php';

$im = Vips\ImageClass::newFromFile($argv[1], ["access" => "sequential"]);

$im = $im->crop(100, 100, $im->width - 200, $im->height - 200);

$im = $im->reduce(1.0 / 0.9, 1.0 / 0.9, ["kernel" => "linear"]);

$mask = Vips\ImageClass::newFromArray(
		  [[-1,  -1, -1], 
		   [-1,  16, -1], 
		   [-1,  -1, -1]], 8);
$im = $im->conv($mask);

$im->writeToFile($argv[2]);

?>
