<?php
 
class VipsTest extends PHPUnit_Framework_TestCase {
 
  public function testVipsNewFromArray()
  {
    $image = Vips\Image::newFromArray([1, 2, 3]);

    $this->assertTrue($image->width == 3);
  }
 
}
