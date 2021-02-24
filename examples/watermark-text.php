#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
use Jcupitt\Vips;

if(count($argv) != 4) {
    echo("usage: ./watermark-text.php input-image output-image \"some text\"\n");
    exit(1);
}

// we can stream the main image, and we want all frames
$image = Vips\Image::newFromFile($argv[1], [
  'access' => 'sequential', 
  'n' => -1
]);

$output_filename = $argv[2];
$text = $argv[3];

// the size of each frame
$page_height = $image->get('page-height');

$text_mask = Vips\Image::text($text, ['width' => $image->width, 'dpi' => 150]);

// semi-transparent white text on a blue background
$foreground = [255, 255, 255, 50];
$background = [0, 0, 255, 50];

// and a 10-pixel marghin
$margin = 10;

$overlay = $text_mask->ifthenelse($foreground, $background, ['blend' => TRUE ]);

// add a margin, with the same background
$overlay = $overlay->embed($margin, $margin, 
  $overlay->width + 2 * $margin, $overlay->height + 2 * $margin, 
  ['extend' => 'background', 'background' => $background]);

// tag as srgb
$overlay = $overlay->copy(['interpretation' => 'srgb']);

// expand to the size of a frame, transparent background, place at the bottom
// left
$overlay = $overlay->embed($margin, $page_height - $overlay->height - $margin, 
  $image->width, $page_height);

// expand to the full size of the gif roll
$overlay = $overlay->replicate(1, $image->height / $page_height);

// composite on top of the gif
$image = $image->composite2($overlay, 'over');

// and write back
$image->writeToFile($output_filename);
