<?php

use Vips\Image\Image;

class VipsConvenienceTest extends PHPUnit_Framework_TestCase 
{

    protected function setUp()
    {
        $filename = dirname(__FILE__) . "/images/img_0076.jpg";
        $this->image = Image::newFromFile($filename);

        /* The original value of pixel (0, 0).
         */
        $this->pixel = $this->image->getpoint(0, 0);
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
        $if = $this->image->more(34)->ifthenelse(255, $this->image);
        $pixel = $if->getpoint(0, 0);
        $this->assertEquals($pixel, [255, 255, 34]);

        $if = $this->image->more(34)->ifthenelse($this->image, 255);
        $pixel = $if->getpoint(0, 0);
        $this->assertEquals($pixel, [39, 38, 255]);

        $if = $this->image->more(34)->ifthenelse(128, 255);
        $pixel = $if->getpoint(0, 0);
        $this->assertEquals($pixel, [128, 128, 255]);
    }

    public function testVipsMaxpos()
    {
        $image = Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
        $result = $image->maxpos();

        $this->assertEquals($result, [6, 2, 1]);
    }

    public function testVipsBandrank()
    {
        $vips = $this->image->bandrank($this->image);
        $result = $vips->getpoint(0, 0);

        $this->assertEquals($result, [39, 38, 34]);
    }

}