<?php

use Jcupitt\Vips;

class VipsMetaTest extends PHPUnit_Framework_TestCase 
{

  public function testVipsSetGet()
  {
    $filename = dirname(__FILE__) . "/images/img_0076.jpg";
    $image = VIps\Image::newFromFile($filename, ["shrink" => 2]);

    $image->poop = "banana";
    $value = $image->poop;

    $this->assertEquals($value, "banana");
  }

}
