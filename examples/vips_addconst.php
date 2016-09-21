#!/usr/bin/env php
<?php 

include '../src/Image.php';

use Vips\Image\Image;

$image = Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
$image = $image->linear(1, 1);

?>
