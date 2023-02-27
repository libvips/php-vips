#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Jcupitt\Vips;
use Jcupitt\Vips\VipsSource;

$source = VipsSource::newFromFile(dirname(__DIR__) . '/tests/images/img_0076.jpg');
$target = Vips\VipsTarget::newToFile(dirname(__DIR__) . "/tests/images/target.jpg");
$image = Vips\Image::newFromSource($source);
$image->writeToTarget($target, '.jpg[Q=95]');
