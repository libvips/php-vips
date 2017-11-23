#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Jcupitt\Vips;

/* Load an image with libvips, render to a large memory buffer, wrap a imagick
 * image around that, then use imagick to save as another file.
 */

$image = Vips\Image::newFromFile($argv[1], 
    ['access' => Vips\Access::SEQUENTIAL]);
$image = $image->colourspace(Vips\Interpretation::RGB16);
$bin = $image->writeToMemory();
$imagick = new \Imagick();
$imagick->setSize($image->width, $image->height);
$imagick->setFormat("rgb");
$imagick->readImageBlob($bin, "rgb");
$imagick->writeImage($argv[2]);

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
