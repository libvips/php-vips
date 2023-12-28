#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';
use Jcupitt\Vips;

#Vips\Config::setLogger(new Vips\DebugLogger());

if (count($argv) != 4) {
    echo("usage: ./animate-image.php input-image output-image 'text string'\n");
    exit(1);
}

$image = Vips\Image::newFromFile($argv[1]);
$text = Vips\Image::text($argv[3], ["dpi" => 300, "rgba" => true]);
$animation = null;
$delay = [];

for ($x = 0; $x < $image->width + $text->width; $x += 10) {
    // append the frame to the image vertically ... we make a very tall, thin
    // strip of frames to save
    $frame = $image->composite2($text, "over", [
        "x" => $x - $text->width,
        "y" => $image->height / 2 - $text->height / 2
    ]);
    if ($animation == null) {
        $animation = $frame;
    }
    else {
        $animation = $animation->join($frame, "vertical");
    }

    // frame delay in ms
    array_push($delay, 30);
}

// set animation properties
$animation->set("delay", $delay);
$animation->set("loop", 0);
$animation->set("page-height", $image->height);

$animation->writeToFile($argv[2]);
