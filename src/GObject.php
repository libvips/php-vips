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
     */
    private static array $handleTable = [];
    private static int $nextIndex = 0;

    /**
     * A pointer to the underlying GObject.
     *
     * @internal
     */
    private CData $pointer;

    /**
     * Upstream objects we must keep alive.
     *
     * A set of references to other php objects which this object refers to
     * via ffi, ie. references which the php GC cannot find automatically.
     *
     * @internal
     */
    private array $_references = [];

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
     *
     * The callback will be triggered every time this signal is issued on this 
     * instance.
     *
     * @throws Exception
     */
    public function signalConnect(string $name, Closure $callback): void
    {
        echo "signalConnect:\n";

        $marshal = self::getMarshal($name);
        $handle = self::getHandle($callback);

        $sig = \FFI::arrayType(FFI::ctypes("GCallback"), [1]);
        $c_callback = \FFI::new($sig);
        $c_callback[0] = $marshal;
            
        $id = FFI::gobject()->g_signal_connect_data($this->pointer, 
                                                    $name, 
                                                    $c_callback[0],
                                                    $handle, 
                                                    null,
                                                    0);
        if ($id === 0) {
            throw new Exception("unable to connect signal $name");
        }

        echo "signalConnect: done\n";
    }

    /* Ideally, we'd use the "user" pointer of signal_connect to hold the
     * callback closure, but unfortunately php-ffi has no way (I think) to 
     * turn a C pointer to function back into a php function, so we have to
     * build a custom closure for every signal connect with the callback
     * embedded within it.
     */
    private static function getMarshal(string $name) : CData
    {
        switch ($name) {
            case 'preeval':
            case 'eval':
            case 'posteval':
                $marshal = static function (
                    CData $imagePointer,
                    CData $progressPointer,
                    CData $handle) : void {
                    $image = new Image($imagePointer);
                    // Image() will unref on gc, so we must ref
                    FFI::gobject()->g_object_ref($imagePointer);

                    // FIXME ... maybe wrap VipsProgress as a php class?
                    $progress = \FFI::cast(FFI::ctypes("VipsProgress"), 
                                           $progressPointer);

                    $callback = self::fromHandle($handle);

                    $callback($image, $progress);
                };

                $sig = \FFI::arrayType(FFI::ctypes("GCallback_progress"), [1]);
                $c_callback = \FFI::new($sig);
                $c_callback[0] = $marshal;

                return \FFI::cast(FFI::ctypes("GCallback"), $c_callback[0]);

            case 'read':
                if (FFI::atLeast(8, 9)) {
                    $marshal = function (
                        CData $sourcePointer,
                        CData $bufferPointer,
                        int $bufferLength,
                        CData $handle) : int {
                        echo "hello from read marshal!\n";
                        $callback = self::fromHandle($handle);
                        $result = 0;

                        $returnBuffer = $callback($bufferLength);
                        if ($returnBuffer !== null) {
                            $result = strlen($returnBuffer);
                            \FFI::memcpy($bufferPointer, 
                                $returnBuffer, 
                                $result
                            );
                        }

                        return $result;
                    };

                    $sig = \FFI::arrayType(FFI::ctypes("GCallback_read"), [1]);
                    $c_callback = \FFI::new($sig);
                    $c_callback[0] = $marshal;

                    echo "c_callback[0] = ";
                    print_r($c_callback[0]);
                    echo "\n";

                    return \FFI::cast(FFI::ctypes("GCallback"), $c_callback[0]);
                }
        }

        throw new Exception("unsupported signal $name");
    }

    private static function getMarshaler(string $name, Closure $callback): ?Closure
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
                    assert($numberOfParams === 3);
                    /**
                     * Signature: void(VipsImage* image, void* progress, void* handle)
                     */
                    $vi = \FFI::cast(
                        FFI::ctypes('GObject'),
                        FFI::gobject()->g_value_get_pointer(\FFI::addr($params[0]))
                    );
                    FFI::gobject()->g_object_ref($vi);
                    $image = new Image($vi);
                    $pr = \FFI::cast(
                        FFI::ctypes('VipsProgress'),
                        FFI::gobject()->g_value_get_pointer(\FFI::addr($params[1]))
                    );
                    $callback($image, $pr);
                };

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
                        assert($numberOfParams === 4);
                        /*
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
                        assert($numberOfParams === 4);
                        /*
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
                        assert($numberOfParams === 2);
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
                        assert($numberOfParams === 2);
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

    private static function getHandle($object) : CData
    {
        $index = self::$nextIndex;
        self::$nextIndex += 1;

        self::$handleTable[$index] = $object;

        // hide the index inside a void*
        $x = \FFI::new(FFI::ctypes("GType"));
        $x->cdata = $index;

        return \FFI::cast("void*", $x);
    }

    private static function fromHandle($handle)
    {
        // recover the index from a void*
        $index = $handle->cdata;

        return self::$handleTable[$index];
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
