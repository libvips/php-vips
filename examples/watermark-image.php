#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
use Jcupitt\Vips;

Vips\Config::setLogger(new Vips\DebugLogger());

$image = Vips\Image::newFromFile($argv[1], ['access' => 'sequential']);
$overlay = $image->add(20)->bandjoin(128);
$overlay = $overlay->cast(Vips\BandFormat::UCHAR);
$comp = $image->composite($overlay, Vips\BlendMode::OVER);
$comp->writeToFile($argv[2]);

exit;


if(count($argv) != 4) {
    echo("usage: ./watermark.php input-image output-image watermark-image\n");
    exit(1);
}

// we can stream the main image
$image = Vips\Image::newFromFile($argv[1], ['access' => 'sequential']);

// we'll read the watermark image many times, so we need random access for this
$watermark = Vips\Image::newFromFile($argv[3]);

// the watermark image needs to have an alpha channel 
if(!$watermark->hasAlpha() || $watermark->bands != 4) {
    echo("watermark image is not RGBA\n");
    exit(1);
}

// make the watermark semi-transparent
$watermark = $watermark->multiply([1, 1, 1, 0.3])->cast("uchar");

// repeat the watermark to the size of the image
$watermark = $watermark->replicate(
    1 + $image->width / $watermark->width,
    1 + $image->height / $watermark->height);
$watermark = $watermark->crop(0, 0, $image->width, $image->height);

// composite the watermark over the main image
$image = $image->composite2($watermark, 'over');

$image->writeToFile($argv[2]);
