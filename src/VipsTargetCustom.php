<?php

namespace Jcupitt\Vips;

use Closure;

class VipsTargetCustom extends VipsTarget
{
    public function __construct()
    {
        $pointer = \FFI::cast(FFI::ctypes('VipsTarget'), FFI::vips()->vips_target_custom_new());
        parent::__construct($pointer);
    }

    public function onWrite(Closure $callback): void
    {
        $this->signalConnect('write', $callback);
    }

    public function onRead(Closure $callback): void
    {
        if (FFI::atLeast(8, 13)) {
            $this->signalConnect('read', static function (string &$buffer) use ($callback): int {
                $chunk = $callback(strlen($buffer));

                if ($chunk === null) {
                    return 0;
                }
                $buffer = substr_replace($buffer, $chunk, 0);
                return strlen($chunk);
            });
        }
    }

    public function onSeek(Closure $callback): void
    {
        if (FFI::atLeast(8, 13)) {
            $this->signalConnect('seek', $callback);
        }
    }

    public function onEnd(Closure $callback): void
    {
        if (FFI::atLeast(8, 13)) {
            $this->signalConnect('end', $callback);
        } else {
            $this->onFinish($callback);
        }
    }

    public function onFinish(Closure $callback): void
    {
        $this->signalConnect('finish', $callback);
    }
}
