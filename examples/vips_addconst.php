#!/usr/bin/env php
<?php 

include '../src/ImageClass.php';

$image = Vips\ImageClass::newFromArray([[1, 2, 3], [4, 5, 6]]);
$image = $image->linear(1, 1);

?>
