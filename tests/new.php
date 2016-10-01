<?php

use Jcupitt\Vips;

class VipsNewTest extends PHPUnit_Framework_TestCase 
{

  public function testVipsNewFromArray()
  {
    $image = Vips\Image::newFromArray([1, 2, 3]);

    $this->assertEquals($image->width, 3);
    $this->assertEquals($image->height, 1);
    $this->assertEquals($image->bands, 1);

    $image = Vips\Image::newFromArray([1, 2, 3], 8, 12);
    $this->assertEquals($image->width, 3);
    $this->assertEquals($image->height, 1);
    $this->assertEquals($image->bands, 1);
    $this->assertEquals($image->scale, 8);
    $this->assertEquals($image->offset, 12);
  }

  public function testVipsNewFromFile()
  {
    $filename = dirname(__FILE__) . "/images/img_0076.jpg";
    $image = Vips\Image::newFromFile($filename, ["shrink" => 2]);

    $this->assertEquals($image->width, 800);
    $this->assertEquals($image->height, 600);
    $this->assertEquals($image->bands, 3);
  }

  public function testVipsNewFromBuffer()
  {
    $filename = dirname(__FILE__) . "/images/img_0076.jpg";
    $buffer = file_get_contents($filename);
    $image = Vips\Image::newFromBuffer($buffer, "", ["shrink" => 2]);

    $this->assertEquals($image->width, 800);
    $this->assertEquals($image->height, 600);
    $this->assertEquals($image->bands, 3);
  }

}
