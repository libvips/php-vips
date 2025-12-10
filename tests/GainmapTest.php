<?php

namespace Jcupitt\Vips\Test;

use Generator;
use Jcupitt\Vips;
use PHPUnit\Framework\TestCase;

class GainmapTest extends TestCase
{
    /**
     * @var Vips\Image
     */
    private $image;

    /**
     * @var Vips\Image
     */
    private $no_gainmap_image;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Vips\FFI::atLeast(8, 18)) {
            $this->markTestSkipped('libvips too old for gainmap test');
        }

        $filename = __DIR__ . '/images/ultra-hdr.jpg';
        $this->image = Vips\Image::newFromFile($filename);

        $filename = __DIR__ . '/images/img_0076.jpg';
        $this->no_gainmap_image = Vips\Image::newFromFile($filename);
    }

    protected function tearDown(): void
    {
        unset($this->image);
        unset($this->no_gainmap_image);
    }

    public function testVipsGetGainmap()
    {
        $gainmap = $this->image->getGainmap();
        $this->assertEquals($gainmap->width, 960);

        $gainmap = $this->no_gainmap_image->getGainmap();
        $this->assertEquals($gainmap, null);
    }

    public function testVipsSetGainmap()
    {
        $gainmap = $this->image->getGainmap();
        $new_gainmap = $gainmap->crop(0, 0, 10, 10);
        $image = $this->image->copy();
        $image->set("gainmap", $new_gainmap);

        $gainmap = $image->getGainmap();
        $this->assertEquals($gainmap->width, 10);
    }
}
