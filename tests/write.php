<?php

use Vips\Image\Image;

class VipsWriteTest extends PHPUnit_Framework_TestCase 
{

  function setUp()
  {
    $this->tmps[] = array();
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
    $filename = dirname(__FILE__) . "/images/img_0076.jpg";
    $image = Image::newFromFile($filename, ["shrink" => 2]);
    $output_filename = $this->tmp(".tif");
    $image->writeToFile($output_filename);
    $image = Image::newFromFile($output_filename);

    $this->assertEquals($image->width, 800);
    $this->assertEquals($image->height, 600);
    $this->assertEquals($image->bands, 3);
  }

  public function testVipsWriteToBuffer()
  {
    $filename = dirname(__FILE__) . "/images/img_0076.jpg";
    $image = Image::newFromFile($filename, ["shrink" => 2]);

    $buffer1 = $image->writeToBuffer(".jpg");
    $output_filename = $this->tmp(".jpg");
    $image->writeToFile($output_filename);
    $buffer2 = file_get_contents($output_filename);

    $this->assertEquals($buffer1, $buffer2); 
  }

}
