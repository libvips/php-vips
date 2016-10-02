<?php

use Jcupitt\Vips;

class VipsCallTest extends PHPUnit_Framework_TestCase 
{
    public function testVipsCall()
    {
        $image = Vips\Image::newFromArray([1, 2, 3]);
        $image = $image->embed(10, 20, 3000, 2000, ["extend" => "copy"]);

        $this->assertEquals($image->width, 3000);
        $this->assertEquals($image->height, 2000);
        $this->assertEquals($image->bands, 1);
    }

    public function testVipsCallStatic()
    {
        $image = Vips\Image::black(1, 2, ["bands" => 3]);

        $this->assertEquals($image->width, 1);
        $this->assertEquals($image->height, 2);
        $this->assertEquals($image->bands, 3);
    }

    public function testVipsCall2D()
    {
        $image = Vips\Image::black(2, 2);
        $image = $image->add([[1, 2], [3, 4]]);

        $this->assertEquals($image->width, 2);
        $this->assertEquals($image->height, 2);
        $this->assertEquals($image->bands, 1);
        $this->assertEquals($image->getpoint(0, 0), [1]);
    }

}
