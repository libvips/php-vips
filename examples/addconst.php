#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Jcupitt\Vips;

$image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
$image = $image->linear(1, 1);
