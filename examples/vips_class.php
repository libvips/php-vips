#!/usr/bin/env php
<?php

include '../src/Image.php';

use Vips\Image\Image;

$image = Image::newFromFile($argv[1]); 

echo "width = ", $image->width, "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);

?>
