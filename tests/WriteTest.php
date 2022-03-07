<?php

namespace Jcupitt\Vips\Test;

use Jcupitt\Vips;
use PHPUnit\Framework\TestCase;

class WriteTest extends TestCase
{
    /**
     * @var array
     */
    private $tmps;

    protected function setUp(): void
    {
        $this->tmps = [];
    }

    protected function tearDown(): void
    {
        foreach ($this->tmps as $tmp) {
            @unlink($tmp);
        }
    }

    public function tmp($suffix)
    {
        $tmp = tempnam(sys_get_temp_dir(), 'vips-test');
        unlink($tmp);
        // race condition, sigh
        $tmp .= $suffix;
        $this->tmps[] = $tmp;
        return $tmp;
    }

    public function testVipsWriteToFile()
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
        $image = Vips\Image::newFromFile($filename, ['shrink' => 8]);
        $output_filename = $this->tmp('.jpg');
        $image->writeToFile($output_filename);
        $image = Vips\Image::newFromFile($output_filename);

        $this->assertEquals($image->width, 200);
        $this->assertEquals($image->height, 150);
        $this->assertEquals($image->bands, 3);
    }

    public function testVipsWriteToBuffer()
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
        $image = Vips\Image::newFromFile($filename, ['shrink' => 8]);

        $buffer1 = $image->writeToBuffer('.jpg');
        $output_filename = $this->tmp('.jpg');
        $image->writeToFile($output_filename);
        $buffer2 = file_get_contents($output_filename);

        $this->assertEquals($buffer1, $buffer2);
    }

    public function testVipsWriteToMemory()
    {
        $binaryStr = pack('C*', ...array_fill(0, 200, 0));
        $image = Vips\Image::newFromMemory($binaryStr, 20, 10, 1, Vips\BandFormat::UCHAR);
        $memStr = $image->writeToMemory();

        $this->assertEquals($binaryStr, $memStr);
    }

    public function testVipsWriteToArray()
    {
        $filename = __DIR__ . '/images/img_0076.jpg';
        $image = Vips\Image::newFromFile($filename, ['shrink' => 8]);
        $array = $image->crop(0, 0, 2, 2)->writeToArray();

        $this->assertEquals(
            $array,
            [34, 39, 35, 44, 49, 45, 67, 52, 49, 120, 105, 102]
        );
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
