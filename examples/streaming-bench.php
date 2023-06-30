#!/usr/bin/env php
<?php

use Jcupitt\Vips\Config;
use Jcupitt\Vips\Image;
use Jcupitt\Vips\Source;
use Jcupitt\Vips\SourceResource;
use Jcupitt\Vips\Target;
use Jcupitt\Vips\TargetResource;

require dirname(__DIR__) . '/vendor/autoload.php';

$doBenchmark = static function () {
    $sourceOptions = ['access' => 'sequential'];
    $sourceOptionString = 'access=sequential';
    $iterations = 100;
    $targetWidth = 100.0;
    $targetSuffix = '.jpg';
    $targetOptions = ['optimize-coding' => true, 'strip' => true, 'Q' => 100, 'profile' => 'srgb'];
    $targetFile = dirname(__DIR__) . "/tests/images/target.jpg";
    $sourceFile = dirname(__DIR__) . '/tests/images/img_0076.jpg';

### Callbacks
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $source = new SourceResource(fopen($sourceFile, 'rb'));
        $target = new TargetResource(fopen($targetFile, 'wb+'));
        $image = Image::newFromSource($source, '', $sourceOptions);
        $image = $image->resize($targetWidth / $image->width);
        $image->writeToTarget(
            $target,
            $targetSuffix,
            $targetOptions
        );
        unlink($targetFile);
    }

    echo (microtime(true) - $start) . ' Seconds for Streaming with callbacks' . PHP_EOL;

### Builtin
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $source = Source::newFromFile($sourceFile);
        $target = Target::newToFile($targetFile);
        $image = Image::newFromSource($source, '', $sourceOptions);
        $image = $image->resize($targetWidth / $image->width);
        $image->writeToTarget(
            $target,
            $targetSuffix,
            $targetOptions
        );
        unlink($targetFile);
    }

    echo (microtime(true) - $start) . ' Seconds for Streaming with builtin source/target' . PHP_EOL;

### Callbacks Thumbnail
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $source = new SourceResource(fopen($sourceFile, 'rb'));
        $target = new TargetResource(fopen($targetFile, 'wb+'));
        $image = Image::thumbnail_source($source, $targetWidth);
        $image->writeToTarget(
            $target,
            $targetSuffix,
            $targetOptions
        );
        unlink($targetFile);
    }

    echo (microtime(true) - $start) . ' Seconds for Streaming Thumbnail with callbacks' . PHP_EOL;

### Builtin Thumbnail
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $source = Source::newFromFile($sourceFile);
        $target = Target::newToFile($targetFile);
        $image = Image::thumbnail_source($source, $targetWidth);
        $image->writeToTarget(
            $target,
            $targetSuffix,
            $targetOptions
        );
        unlink($targetFile);
    }

    echo (microtime(true) - $start) . ' Seconds for Streaming Thumbnail with builtin source/target' . PHP_EOL;

### Thumbnail
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $image = Image::thumbnail($sourceFile . "[$sourceOptionString]", $targetWidth);
        $image->writeToFile(
            $targetFile,
            $targetOptions
        );
        unlink($targetFile);
    }

    echo (microtime(true) - $start) . ' Seconds for Thumbnail API' . PHP_EOL;

### Classic
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $image = Image::newFromFile($sourceFile, $sourceOptions);
        $image = $image->resize($targetWidth / $image->width);
        $image->writeToFile(
            $targetFile,
            $targetOptions
        );
        unlink($targetFile);
    }

    echo (microtime(true) - $start) . ' Seconds for Classic API' . PHP_EOL;
};

$doBenchmark();

//echo "=== NOW NO CACHE ===" . PHP_EOL;
//
//Config::cacheSetMax(0);
//Config::cacheSetMaxFiles(0);
//Config::cacheSetMaxMem(0);
//
//$doBenchmark();
