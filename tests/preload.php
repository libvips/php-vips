<?php

// preload script used by PreloadTest

require dirname(__DIR__) . '/vendor/autoload.php';

// twice, to check that preloading is idempotent
Jcupitt\Vips\FFI::preload();
Jcupitt\Vips\FFI::preload();
