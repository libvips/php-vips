<?php

namespace Jcupitt\Vips\Test;

use Jcupitt\Vips;
use PHPUnit\Framework\TestCase;

class CallTest extends TestCase
{
    public function testVipsCall()
    {
        $image = Vips\Image::newFromArray([1, 2, 3]);
        $image = $image->embed(10, 20, 3000, 2000, [
            'extend' => Vips\Extend::COPY
        ]);

        $this->assertEquals($image->width, 3000);
        $this->assertEquals($image->height, 2000);
        $this->assertEquals($image->bands, 1);
    }

    public function testVipsCallHyphen()
    {
        # should work with "-" as well as "_"
        $image = Vips\Image::worley(64, 64, [
            "cell-size" => 8
        ]);

        $this->assertEquals($image->width, 64);
        $this->assertEquals($image->height, 64);
        $this->assertEquals($image->bands, 1);
    }

    public function testVipsCallStatic()
    {
        $image = Vips\Image::black(1, 4, ['bands' => 3]);

        $this->assertEquals($image->width, 1);
        $this->assertEquals($image->height, 4);
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

    public function testVipsDraw()
    {
        $image = Vips\Image::black(100, 100);
        $image = $image->draw_circle(255, 50, 50, 20, ['fill' => true]);

        $this->assertEquals($image->width, 100);
        $this->assertEquals($image->height, 100);
        $this->assertEquals($image->bands, 1);
        $this->assertEquals($image->getpoint(0, 0), [0]);
        $this->assertEquals($image->getpoint(50, 50), [255]);
    }
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
