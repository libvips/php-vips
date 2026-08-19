<?php

namespace Jcupitt\Vips\Test;

use PHPUnit\Framework\TestCase;

class PreloadTest extends TestCase
{
    public function testPreload()
    {
        if (!function_exists('opcache_get_status')) {
            $this->markTestSkipped('no opcache');
        }

        /* opcache.preload can only be set at startup, so we have to use a
         * subprocess.
         */
        $command = escapeshellarg(PHP_BINARY) . ' -d opcache.enable_cli=1';

        /* Before php 8.3, preloading as root fails unless you name the user
         * to drop to. php warns if you set it when you are not root.
         */
        if (function_exists('posix_geteuid') && posix_geteuid() === 0) {
            $command .= ' -d opcache.preload_user=root';
        }

        $output = shell_exec(
            $command .
            ' -d opcache.preload=' . escapeshellarg(__DIR__ . '/preload.php') .
            ' -r ' . escapeshellarg(
                '$status = opcache_get_status();' .
                'echo in_array("Jcupitt\\Vips\\Image", ' .
                '$status["preload_statistics"]["classes"]) ? "preloaded" : "missing";'
            ) . ' 2>&1'
        );

        $this->assertStringNotContainsString("Can't preload", $output);
        $this->assertStringContainsString('preloaded', $output);
    }
}
