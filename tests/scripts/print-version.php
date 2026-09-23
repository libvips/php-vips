<?php

/*
 * Boots php-vips and prints the libvips version. Run by StartupTest in a
 * separate PHP process so that ffi.enable, which is INI_SYSTEM, can be set
 * per test.
 */

require __DIR__ . '/../../vendor/autoload.php';

echo Jcupitt\Vips\Config::version(), "\n";
