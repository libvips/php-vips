<?php

use Jcupitt\Vips;

class VipsWriteTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $tmps;

    function setUp()
    {
        $this->tmps = [];
    }

    function tearDown()
    {
        foreach ($this->tmps as $tmp) {
          @unlink($tmp);
        }
    }

    function tmp($suffix)
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
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
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
        $filename = dirname(__FILE__) . '/images/img_0076.jpg';
        $image = Vips\Image::newFromFile($filename, ['shrink' => 8]);

        $buffer1 = $image->writeToBuffer('.jpg');
        $output_filename = $this->tmp('.jpg');
        $image->writeToFile($output_filename);
        $buffer2 = file_get_contents($output_filename);

        $this->assertEquals($buffer1, $buffer2); 
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
