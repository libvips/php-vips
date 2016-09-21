#!/usr/bin/env php
<?php 

include '../src/Image.php';

use JCupitt\Vips;

$image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
$image = $image->linear(1, 1);

?>
