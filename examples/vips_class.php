#!/usr/bin/env php
<?php

include '../src/Image.php';

use Jcupitt\Vips;

Vips\Image::setLogging(true);

$image = Vips\Image::newFromFile($argv[1]); 

echo "width = ", $image->width, "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);

?>
