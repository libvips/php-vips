<?php

use Jcupitt\Vips;

class VipsMainTest extends PHPUnit_Framework_TestCase 
{
    public function testVipsCacheSetMax()
    {
        /* Not easy to test ... just make sure it can execute.
         */
        Vips\Main\cacheSetMax(12);
    }

    public function testVipsCacheSetMaxMem()
    {
        Vips\Main\cacheSetMaxMem(12);
    }

    public function testVipsCacheSetMaxFiles()
    {
        Vips\Main\cacheSetMaxFiles(12);
    }

    public function testVipsConcurrencySet()
    {
        Vips\Main\concurrencySet(12);
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
