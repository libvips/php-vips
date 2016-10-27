#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Jcupitt\Vips;

Vips\Config::setLogger(new Vips\DebugLogger);

$image = Vips\Image::newFromFile($argv[1]);

echo "width = " . $image->width . "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);
