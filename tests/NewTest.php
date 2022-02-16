<?php

namespace Jcupitt\Vips\Test;

use Jcupitt\Vips;
use PHPUnit\Framework\TestCase;

class NewTest extends TestCase
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
        $filename = __DIR__ . '/images/img_0076.jpg';
        $image = Vips\Image::newFromFile($filename);

        $this->assertEquals($image->width, 1600);
        $this->assertEquals($image->height, 1200);
        $this->assertEquals($image->bands, 3);
    }

    public function testVipsNewFromImage()
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
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

        $image2 = $image->newFromImage([1, 2, 3]);

        $this->assertEquals($image2->bands, 3);
        $this->assertEquals($image2->avg(), 2);
    }

    public function testVipsNewFromBuffer()
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
        $buffer = file_get_contents($filename);
        $image = Vips\Image::newFromBuffer($buffer);

        $this->assertEquals($image->width, 1600);
        $this->assertEquals($image->height, 1200);
        $this->assertEquals($image->bands, 3);
    }

    public function testVipsCopyMemory()
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
        $image1 = Vips\Image::newFromFile($filename);
        $image2 = $image1->copyMemory();

        $this->assertEquals($image1->avg(), $image2->avg());
    }

    public function testVipsNewInterpolator()
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
        $image1 = Vips\Image::newFromFile($filename);
        $interp = Vips\Image::newInterpolator('bicubic');
        $image2 = $image1->affine([2, 0, 0, 2], ['interpolate' => $interp]);

        $widthInput = $image1->width;
        $widthOutput = $image2->width;

        $this->assertNotNull($interp);
        $this->assertEquals($widthInput * 2, $widthOutput);
    }

    public function testVipsNewFromMemory()
    {
        $binaryStr = pack('C*', ...array_fill(0, 200, 0));
        $image = Vips\Image::newFromMemory($binaryStr, 20, 10, 1, Vips\BandFormat::UCHAR);

        $this->assertEquals($image->width, 20);
        $this->assertEquals($image->height, 10);
        $this->assertEquals($image->format, Vips\BandFormat::UCHAR);
        $this->assertEquals($image->bands, 1);
        $this->assertEquals($image->avg(), 0);
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
