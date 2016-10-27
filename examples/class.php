#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Jcupitt\Vips;

Vips\Main::setLogger(new Vips\Logger);

$image = Vips\Image::newFromFile($argv[1]);

echo "width = " . $image->width . "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);
