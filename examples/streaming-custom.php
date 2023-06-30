#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Jcupitt\Vips;

if (count($argv) != 4) {
    echo "usage: $argv[0] IN-FILE OUT-FILE FORMAT\n";
    echo "  eg.: $argv[0] ~/pics/k2.jpg x.tif .tif[tile,pyramid]\n";
    exit(1);
}
    
$in_file = fopen($argv[1], 'r');
$source = new Vips\SourceCustom();
$source->onRead(function ($bufferLength) use (&$in_file) {
    // return 0 for EOF, -ve for read error
    return fread($in_file, $bufferLength);
});
// seek is optional
$source->onSeek(function ($offset, $whence) use (&$in_file) {
    if (fseek($in_file, $offset, $whence)) {
        return -1;
    }
    
    return ftell($in_file);
});

// open for write and read ... formats like tiff need to be able to seek back
// in the output and update bytes later
$out_file = fopen($argv[2], 'w+');
$target = new Vips\TargetCustom();
$target->onWrite(function ($buffer) use (&$out_file) {
    $result = fwrite($out_file, $buffer);
    if ($result === false) {
        // IO error
        return -1;
    } else {
        return $result;
    }
});
// read and seek are optional
$target->onSeek(function ($offset, $whence) use (&$out_file) {
    if (fseek($out_file, $offset, $whence)) {
        return -1;
    }

    return ftell($out_file);
});
$target->onRead(function ($bufferLength) use (&$out_file) {
    return fread($out_file, $bufferLength);
});

$image = Vips\Image::newFromSource($source);
$image->writeToTarget($target, $argv[3]);
