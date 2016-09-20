<?php

use Vips\Image\Image;

class VipsTest extends PHPUnit_Framework_TestCase 
{

  public function testVipsWriteToFile()
  {
    $filename = dirname(__FILE__) . "/images/img_0076.jpg";
    $image = Image::newFromFile($filename, ["shrink" => 2]);

    $this->assertEquals($image->width, 800);
    $this->assertEquals($image->height, 600);
    $this->assertEquals($image->bands, 3);

    $output_filename = dirname(__FILE__) . "/x.tif";

  }

}
