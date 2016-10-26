#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Jcupitt\Vips;

    public static function setLogger(LoggerInterface $logger)

$image = Vips\Image::newFromFile($argv[1]);

echo "width = " . $image->width . "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);
