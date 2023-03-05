#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Jcupitt\Vips;

if (count($argv) != 3) {
    echo("usage: ./bench.php input-image output-image\n");
    exit(1);
}

$im = Vips\Image::newFromFile($argv[1], ['access' => Vips\Access::SEQUENTIAL]);

$im = $im->crop(100, 100, $im->width - 200, $im->height - 200);

$im = $im->reduce(1.0 / 0.9, 1.0 / 0.9, ['kernel' => Vips\Kernel::LINEAR]);

$mask = Vips\Image::newFromArray([
    [-1,  -1, -1],
    [-1,  16, -1],
    [-1,  -1, -1]
], 8);
$im = $im->conv($mask);

$im->writeToFile($argv[2]);
