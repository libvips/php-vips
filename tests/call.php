<?php

use Vips\Image\Image;

class VipsCallTest extends PHPUnit_Framework_TestCase 
{

  public function testVipsCall()
  {
    $image = Image::newFromArray([1, 2, 3]);
    $image = $image->embed(10, 20, 3000, 2000, ["extend" => "copy"]);

    $this->assertEquals($image->width, 3000);
    $this->assertEquals($image->height, 2000);
    $this->assertEquals($image->bands, 1);
  }

  public function testVipsCallStatic()
  {
    $image = Image::black(1, 2, ["bands" => 3]);

    $this->assertEquals($image->width, 1);
    $this->assertEquals($image->height, 2);
    $this->assertEquals($image->bands, 3);
  }

  public function testVipsBandjoin()
  {
    $image = Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
    $rgb = $image->bandjoin([$image, $image]);

    $this->assertEquals($rgb->width, 3);
    $this->assertEquals($rgb->height, 2);
    $this->assertEquals($rgb->bands, 3);
  }

  public function testVipsAddConst()
  {
    $image = Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
    $image = $image->add(1);
    $pixel = $image->crop(0, 0, 1, 1)->avg();

    $this->assertEquals($pixel, 2);
  }

  public function testVipsGetPoint()
  {
    $image = Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
    $rgb = $image->bandjoin([$image, $image]);
    $rgb = $rgb->add(1);
    $pixel = $rgb->getpoint(0, 0);

    $this->assertEquals($pixel, [2, 2, 2]);
  }

  public function testVipsBandjoinConst()
  {
    $image = Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
    $imagea = $image->bandjoin(255);
    $pixel = $imagea->getpoint(0, 0);

    $this->assertEquals($pixel, [1, 255]);
  }

  public function testVipsIfthenelseConst()
  {
    $filename = dirname(__FILE__) . "/images/img_0076.jpg";
    $image = Image::newFromFile($filename);

    $if = $image->more(34)->ifthenelse(255, $image);
    $pixel = $if->getpoint(0, 0);
    $this->assertEquals($pixel, [255, 255, 34]);

    $if = $image->more(34)->ifthenelse($image, 255);
    $pixel = $if->getpoint(0, 0);
    $this->assertEquals($pixel, [39, 38, 255]);

    $if = $image->more(34)->ifthenelse(128, 255);
    $pixel = $if->getpoint(0, 0);
    $this->assertEquals($pixel, [128, 128, 255]);
  }


}
