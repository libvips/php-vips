<?php

namespace Jcupitt\Vips\Test;

use Jcupitt\Vips;
use PHPUnit\Framework\TestCase;

class ConvenienceTest extends TestCase
{
    /**
     * @var Vips\Image
     */
    private $image;

    /**
     * The original value of pixel (0, 0).
     */
    private $pixel;

    protected function setUp(): void
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
        $this->image = Vips\Image::newFromFile($filename);
        $this->pixel = $this->image->getpoint(0, 0);
    }

    protected function tearDown(): void
    {
        unset($this->image);
        unset($this->pixel);
    }

    public function testVipsBandjoin()
    {
        $image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
        $rgb = $image->bandjoin([$image, $image]);

        $this->assertEquals($rgb->width, 3);
        $this->assertEquals($rgb->height, 2);
        $this->assertEquals($rgb->bands, 3);
    }

    public function testVipsBandsplit()
    {
        $arr = $this->image->bandsplit();

        $this->assertCount(3, $arr);
        $this->assertEquals($arr[0]->bands, 1);
    }

    public function testVipsComposite()
    {
        if (version_compare(Vips\Config::version(), '8.6.0') >= 0) {
            $overlay = $this->image->add(20)->bandjoin(128);
            $overlay = $overlay->cast(Vips\BandFormat::UCHAR);
            $comp = $this->image->composite($overlay, Vips\BlendMode::OVER);
            $this->assertEquals($comp->getpoint(0, 0)[0], $this->pixel[0] + 10);
        }
    }

    public function testVipsAddConst()
    {
        $image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
        $image = $image->add(1);
        $pixel = $image->crop(0, 0, 1, 1)->avg();

        $this->assertEquals($pixel, 2);
    }

    public function testVipsSubtractConst()
    {
        $image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
        $image = $image->subtract(1);
        $pixel = $image->getpoint(1, 1);

        $this->assertCount(1, $pixel);
        $this->assertEquals($pixel[0], 4);
    }

    public function testVipsMultiplyConst()
    {
        $image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
        $image = $image->multiply(2);
        $pixel = $image->getpoint(1, 1);

        $this->assertCount(1, $pixel);
        $this->assertEquals($pixel[0], 10);
    }

    public function testVipsDivideConst()
    {
        $image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
        $image = $image->divide(2);
        $pixel = $image->getpoint(0, 1);

        $this->assertCount(1, $pixel);
        $this->assertEquals($pixel[0], 2);
    }

    public function testVipsGetPoint()
    {
        $image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
        $rgb = $image->bandjoin([$image, $image]);
        $rgb = $rgb->add(1);
        $pixel = $rgb->getpoint(0, 0);

        $this->assertEquals($pixel, [2, 2, 2]);
    }

    public function testVipsBandjoinConst()
    {
        $image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
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

    public function testVipsIfthenelseBlend()
    {
        $mask = $this->image[1];
        $blended = $mask->ifthenelse($mask, [255, 0, 0], ['blend' => true]);

        $pixel = $blended->getpoint(0, 0);
        $this->assertEquals($pixel, [223, 6, 6]);
    }

    public function testVipsMaxpos()
    {
        $image = Vips\Image::newFromArray([[1, 2, 3], [4, 5, 6]]);
        $result = $image->maxpos();

        $this->assertEquals($result, [6, 2, 1]);
    }

    public function testVipsBandrank()
    {
        $vips = $this->image->bandrank($this->image);
        $result = $vips->getpoint(0, 0);

        $this->assertEquals($result, [39, 38, 34]);
    }

    public function testVipsMedian()
    {
        $vips = $this->image->median(5);
        $result = $vips->getpoint(0, 0);

        $this->assertEquals($result, [37, 38, 33]);
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
