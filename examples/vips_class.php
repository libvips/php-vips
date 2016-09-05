#!/usr/bin/env php
<?php

include '../src/ImageClass.php';

$image = Vips\ImageClass::newFromFile($argv[1]); 

echo "width = ", $image->width, "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);

?>
