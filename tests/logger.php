<?php

use Jcupitt\Vips;

class VipsLoggerTest extends PHPUnit_Framework_TestCase
{
    public function testGetLoggerCall()
    {
        // Asserts that getLogger without setting it should
        // return a null value.
        $logger = Vips\Image::getLogger();

        $this->assertNull($logger);
    }

    public function testSetLoggerCall()
    {
        Vips\Image::setLogger(new class implements Psr\Log\LoggerInterface {
            use Psr\Log\LoggerTrait;

            public function log($level, $message, array $context = array())
            {
                // Do logging logic here.
            }
        });

        $logger = Vips\Image::getLogger();

        // Asserts that getLogger should return an instance of Psr\Log\LoggerInterface
        $this->assertInstanceOf(Psr\Log\LoggerInterface::class, $logger);
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
