<?php

namespace Jcupitt\Vips;

use Closure;

class VipsTargetCustom extends VipsTarget
{
    /**
     * A pointer to the underlying VipsTargetCustom. This is the same as the
     * GObject, just cast to VipsTargetCustom to help FFI.
     *
     * @internal
     */
    public \FFI\CData $pointer;

    public function __construct()
    {
        $this->pointer = FFI::vips()->vips_target_custom_new();
        parent::__construct($this->pointer);
    }

    public function onWrite(Closure $callback): void
    {
        $this->signalConnect('write', $callback);
    }

    public function onRead(Closure $callback): void
    {
        if (FFI::atLeast(8, 13)) {
            $this->signalConnect('read', $callback);
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
