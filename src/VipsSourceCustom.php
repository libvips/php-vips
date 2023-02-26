<?php

namespace Jcupitt\Vips;

use Closure;

class VipsSourceCustom extends VipsSource
{
    public function __construct()
    {
        $source = \FFI::cast(FFI::ctypes('VipsSource'), FFI::vips()->vips_source_custom_new());
        parent::__construct($source);
    }

    /**
     * Attach a read handler.
     * The interface is exactly as io.read() in Python. The handler is given a number
     * of bytes to fetch, and should return a bytes-like object containing up
     * to that number of bytes. If there is no more data available, it should
     * return None.
     */
    public function onRead(Closure $callback): void
    {
        $this->signalConnect('read', static function (string &$buffer) use ($callback): int {
            $chunk = $callback(strlen($buffer));

            if ($chunk === null) {
                return 0;
            }

            $buffer = substr_replace($buffer, $chunk, 0);
            return strlen($chunk);
        });
    }

    /**
     * Attach a seek handler.
     * The interface is the same as fseek, so the handler is passed
     * parameters for $offset and $whence with the same meanings.
     * However, the handler MUST return the new seek position. A simple way
     * to do this is to call ftell() and return that result.
     * Seek handlers are optional. If you do not set one, your source will be
     * treated as unseekable and libvips will do extra caching.
     * $whence in particular:
     *  0 => start
     *  1 => current position
     *  2 => end
     */
    public function onSeek(Closure $callback): void
    {
        $this->signalConnect('seek', $callback);
    }
}
