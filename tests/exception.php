<?php

use Jcupitt\Vips;

class VipsExceptionTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var Vips\Image
     */
    private $image;

    /**
     * The original value of pixel (0, 0).
     */
    private $pixel;

    protected function setUp()
    {
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
        $this->image = Vips\Image::newFromFile($filename);
        $this->pixel = $this->image->getpoint(0, 0);
    }

    public function testVipsNewFromFileException()
    {
        $this->expectException(Vips\Exception::class);

        $image = Vips\Image::newFromFile("I don't exist.jpg");
    }

    public function testVipsWriteToFileException()
    {
        $this->expectException(Vips\Exception::class);

        $this->image->writeToFile("/directory/doesn't/exist.jpg");
    }

    public function testVipsWriteToBufferException()
    {
        $this->expectException(Vips\Exception::class);

        $string = $this->image->writeToBuffer('.jpg', ['crazy option' => 42]);
    }

    public function testVipsGetException()
    {
        $this->expectException(Vips\Exception::class);

        $x = $this->image->get("I don't exist");
    }

    public function testVipsOperationException()
    {
        $this->expectException(Vips\Exception::class);

        $x = $this->image->add([1, 2, 3, 4]);
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
