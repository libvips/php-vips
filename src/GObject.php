<?php

/**
 * Vips is a php binding for the vips image processing library
 *
 * PHP version 7
 *
 * LICENSE:
 *
 * Copyright (c) 2016 John Cupitt
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/libvips/php-vips
 */

namespace Jcupitt\Vips;

use Closure;
use FFI\CData;

/**
 * This class holds a pointer to a GObject and manages object lifetime.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/libvips/php-vips
 */
abstract class GObject
{
    /**
     * A pointer to the underlying GObject.
     *
     * @internal
     */
    private CData $pointer;

    /**
     * Wrap a GObject around an underlying vips resource. The GObject takes
     * ownership of the pointer and will unref it on finalize.
     *
     * Don't call this yourself, users should stick to (for example)
     * Image::newFromFile().
     *
     * @param CData $pointer The underlying pointer that this
     *  object should wrap.
     *
     * @internal
     */
    public function __construct(CData $pointer)
    {
        $this->pointer = \FFI::cast(FFI::ctypes("GObject"), $pointer);
    }

    public function __destruct()
    {
        $this->unref();
    }

    public function __clone()
    {
        $this->ref();
    }

    public function ref(): void
    {
        FFI::gobject()->g_object_ref($this->pointer);
    }

    public function unref(): void
    {
        FFI::gobject()->g_object_unref($this->pointer);
    }

    /**
     * Connect to a signal on this object.
     * The callback will be triggered every time this signal is issued on this instance.
     * @throws Exception
     */
    public function signalConnect(string $name, callable $callback): void
    {
        $marshaler = self::getMarshaler($name, $callback);
        if ($marshaler === null) {
            throw new Exception("unsupported signal $name");
        }

        $gc = FFI::newGClosure();
        FFI::gobject()->g_closure_set_marshal($gc, $marshaler);
        FFI::gobject()->g_signal_connect_closure($this->pointer, $name, $gc, 0);
    }

    private static function getMarshaler(string $name, callable $callback): ?Closure
    {
        switch ($name) {
            case 'preeval':
            case 'eval':
            case 'posteval':
                return static function (
                    CData  $gClosure,
                    ?CData $returnValue,
                    int    $numberOfParams,
                    CData  $params,
                    CData  $hint,
                    ?CData $data
                ) use (&$callback) {
                    assert($numberOfParams === 2);
                    /**
                     * Signature: void(VipsImage* image, void* progress, void* handle)
                     */
                    $vi = FFI::gobject()->g_value_get_object(\FFI::addr($params[0]));
                    FFI::gobject()->g_object_ref($vi);
                    $image = new Image($vi);
                    $pr = \FFI::cast(
                        FFI::ctypes('VipsProgress'),
                        FFI::gobject()->g_value_get_pointer(\FFI::addr($params[1]))
                    );
                    $callback($image, $pr);
                };
            case 'read':
                if (FFI::atLeast(8, 9)) {
                    return static function (
                        CData  $gClosure,
                        CData  $returnValue,
                        int    $numberOfParams,
                        CData  $params,
                        CData  $hint,
                        ?CData $data
                    ) use (&$callback): void {
                        assert($numberOfParams === 3);
                        /**
                         * Signature: gint64(VipsSourceCustom* source, void* buffer, gint64 length, void* handle)
                         */
                        $bufferLength = (int)FFI::gobject()->g_value_get_int64(\FFI::addr($params[2]));
                        $returnBuffer = $callback($bufferLength);
                        $returnBufferLength = 0;

                        if ($returnBuffer !== null) {
                            $returnBufferLength = strlen($returnBuffer);
                            $bufferPointer = FFI::gobject()->g_value_get_pointer(\FFI::addr($params[1]));
                            \FFI::memcpy($bufferPointer, $returnBuffer, $returnBufferLength);
                        }
                        FFI::gobject()->g_value_set_int64($returnValue, $returnBufferLength);
                    };
                }

                return null;
            case 'seek':
                if (FFI::atLeast(8, 9)) {
                    return static function (
                        CData  $gClosure,
                        CData  $returnValue,
                        int    $numberOfParams,
                        CData  $params,
                        CData  $hint,
                        ?CData $data
                    ) use (&$callback): void {
                        assert($numberOfParams === 3);
                        /**
                         * Signature: gint64(VipsSourceCustom* source, gint64 offset, int whence, void* handle)
                         */
                        $offset = (int)FFI::gobject()->g_value_get_int64(\FFI::addr($params[1]));
                        $whence = (int)FFI::gobject()->g_value_get_int(\FFI::addr($params[2]));
                        FFI::gobject()->g_value_set_int64($returnValue, $callback($offset, $whence));
                    };
                }

                return null;
            case 'write':
                if (FFI::atLeast(8, 9)) {
                    return static function (
                        CData  $gClosure,
                        CData  $returnValue,
                        int    $numberOfParams,
                        CData  $params,
                        CData  $hint,
                        ?CData $data
                    ) use (&$callback): void {
                        assert($numberOfParams === 3);
                        /**
                         * Signature: gint64(VipsTargetCustom* target, void* buffer, gint64 length, void* handle)
                         */
                        $bufferPointer = FFI::gobject()->g_value_get_pointer(\FFI::addr($params[1]));
                        $bufferLength = (int)FFI::gobject()->g_value_get_int64(\FFI::addr($params[2]));
                        $buffer = \FFI::string($bufferPointer, $bufferLength);
                        $returnBufferLength = $callback($buffer);
                        FFI::gobject()->g_value_set_int64($returnValue, $returnBufferLength);
                    };
                }

                return null;
            case 'finish':
                if (FFI::atLeast(8, 9)) {
                    return static function (
                        CData  $gClosure,
                        ?CData $returnValue,
                        int    $numberOfParams,
                        CData  $params,
                        CData  $hint,
                        ?CData $data
                    ) use (&$callback): void {
                        assert($numberOfParams === 1);
                        /**
                         * Signature: void(VipsTargetCustom* target, void* handle)
                         */
                        $callback();
                    };
                }

                return null;
            case 'end':
                if (FFI::atLeast(8, 13)) {
                    return static function (
                        CData  $gClosure,
                        CData  $returnValue,
                        int    $numberOfParams,
                        CData  $params,
                        CData  $hint,
                        ?CData $data
                    ) use (&$callback): void {
                        assert($numberOfParams === 1);
                        /**
                         * Signature: int(VipsTargetCustom* target, void* handle)
                         */
                        FFI::gobject()->g_value_set_int($returnValue, $callback());
                    };
                }

                return null;
            default:
                return null;
        }
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
