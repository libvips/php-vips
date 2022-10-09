<?php

namespace Jcupitt\Vips\Test;

use Jcupitt\Vips;
use PHPUnit\Framework\TestCase;

class MetaTest extends TestCase
{
    /**
     * @var Vips\Image
     */
    private $image;

    /**
     * @var Vips\Image
     */
    private $png_image;

    protected function setUp(): void
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
        $this->image = Vips\Image::newFromFile($filename);

        $png_filename = __DIR__ . '/images/PNG_transparency_demonstration_1.png';
        $this->png_image = Vips\Image::newFromFile($png_filename);
    }

    protected function tearDown(): void
    {
        unset($this->image);
        unset($this->png_image);
    }

    public function testVipsSetGet()
    {
        $this->image->poop = 'banana';
        $value = $this->image->poop;

        $this->assertEquals($value, 'banana');
    }

    public function testVipsGetExifData()
    {
        $name = 'exif-data';
        $exif = $this->image->$name;

        # 9724 bytes of exif attached ... this should work even without libexif
        $this->assertEquals(strlen($exif), 9724);
    }

    public function testVipsGetThumbnail()
    {
        $thumbnail_data = $this->image->get('jpeg-thumbnail-data');
        $thumb = Vips\Image::newFromBuffer($thumbnail_data);

        $this->assertEquals($thumb->width, 160);
    }

    public function testVipsGetTypeof()
    {
        $gint = $this->image->typeof('width');

        // should always be the same, I think
        $this->assertEquals($gint, 24);
    }

    public function testVipsRemove()
    {
        $image = $this->image->copy();
        $exif = $image->get('exif-data');
        $this->assertEquals(strlen($exif), 9724);

        $image->remove('exif-data');

        $this->expectException(Vips\Exception::class);
        $exif = $image->get('exif-data');
    }

    public function testVipsEnumString()
    {
        $x = $this->image->interpretation;
        $this->assertEquals($x, 'srgb');
    }

    public function testVipsHasAlpha()
    {
        $this->assertEquals($this->image->hasAlpha(), false);
        $this->assertEquals($this->png_image->hasAlpha(), true);
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
