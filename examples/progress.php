#!/usr/bin/env php 
<?php

require __DIR__ . '/../vendor/autoload.php';
use Jcupitt\Vips;

# Vips\Config::setLogger(new Vips\DebugLogger());

$image = Vips\Image::black(1, 1000000);
$image->setProgress(true);
    
$image->signalConnect("preeval", function ($image, $progress) {
    echo "preeval:\n";
});
$image->signalConnect("eval", function ($image, $progress) {
    echo "eval: $progress->percent % complete\r";
});
    
$image->signalConnect("posteval", function ($image, $progress) {
    echo "\nposteval:\n";
});
    
// trigger evaluation
$image->avg();
    
$image = null;

Vips\FFI::shutDown();
