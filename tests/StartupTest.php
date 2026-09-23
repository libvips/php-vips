<?php

namespace Jcupitt\Vips\Test;

use Jcupitt\Vips;
use PHPUnit\Framework\TestCase;

/*
 * ffi.enable is INI_SYSTEM, so it cannot be changed with ini_set(). These
 * tests start a second PHP process with -d ffi.enable=... and check what
 * php-vips does on startup.
 */
class StartupTest extends TestCase
{
    public function testStartsWithDefaultFfiEnable()
    {
        [$output, $result_code] = $this->execFFI("ffi.enable=preload");

        $this->assertEquals(0, $result_code);
        $this->assertEquals(preg_match("/\d+\.\d+\.\d+/", implode("\n", $output)), 1);
    }

    public function testReportsFfiReasonWhenFfiDisabled()
    {
        [$output, $result_code] = $this->execFFI("ffi.enable=0");

        $this->assertNotEquals(0, $result_code);
        $this->assertTrue(strpos(implode("\n", $output), "FFI API is restricted") !== false);
    }

    private function execFFI(string $ffi_enable_arg): array
    {
        exec(
            escapeshellarg(PHP_BINARY)
            ." -d "
            .$ffi_enable_arg . " "
            .escapeshellarg(__DIR__ . "/scripts/print-version.php")
            ." 2>&1",
            $output,
            $result_code
        );

        return [$output, $result_code];
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
