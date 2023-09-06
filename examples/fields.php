#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Jcupitt\Vips;

$im = Vips\Image::newFromFile($argv[1]);

$names = $im->getFields();

echo "$argv[1]\n";
foreach ($names as &$name) {
    $value = $im->get($name);
    echo "$name: $value\n";
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
