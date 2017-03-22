#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Jcupitt\Vips;

Vips\Config::setLogger(new Vips\DebugLogger());

$image = Vips\Image::newFromFile($argv[1]);

echo "width = " . $image->width . "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
