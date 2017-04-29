<?php

use Jcupitt\Vips;

class VipsNewTest extends PHPUnit\Framework\TestCase
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
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
        $image = Vips\Image::newFromFile($filename);

        $this->assertEquals($image->width, 1600);
        $this->assertEquals($image->height, 1200);
        $this->assertEquals($image->bands, 3);
    }

    public function testVipsNewFromImage()
    {
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
        $image = Vips\Image::newFromFile($filename);

        $image2 = $image->newFromImage(12);

        $this->assertEquals($image2->width, $image->width);
        $this->assertEquals($image2->height, $image->height);
        $this->assertEquals($image2->format, $image->format);
        $this->assertEquals($image2->xres, $image->xres);
        $this->assertEquals($image2->yres, $image->yres);
        $this->assertEquals($image2->xoffset, $image->xoffset);
        $this->assertEquals($image2->yoffset, $image->yoffset);
        $this->assertEquals($image2->bands, 1);
        $this->assertEquals($image2->avg(), 12);

        $image2 = $image->newFromImage([1,2,3]);

        $this->assertEquals($image2->bands, 3);
        $this->assertEquals($image2->avg(), 2);
    }

    public function testVipsFindLoad()
    {
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
        $loader = Vips\Image::findLoad($filename);

        $this->assertEquals($loader, 'VipsForeignLoadJpegFile');
    }

    public function testVipsNewFromBuffer()
    {
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
        $buffer = file_get_contents($filename);
        $image = Vips\Image::newFromBuffer($buffer);

        $this->assertEquals($image->width, 1600);
        $this->assertEquals($image->height, 1200);
        $this->assertEquals($image->bands, 3);
    }

    public function testVipsFindLoadBuffer()
    {
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
        $buffer = file_get_contents($filename);
        $loader = Vips\Image::findLoadBuffer($buffer);

        $this->assertEquals($loader, 'VipsForeignLoadJpegBuffer');
    }

    public function testVipsCopyMemory()
    {
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
        $image1 = Vips\Image::newFromFile($filename);
        $image2 = $image1->copyMemory();

        $this->assertEquals($image1->avg(), $image2->avg());
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
