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

/**
 * This class holds a pointer to a VipsOperation (the libvips operation base 
 * class) and manages argument introspection and operation call.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/libvips/php-vips
 */
class VipsOperation extends VipsObject
{
    /**
     * A pointer to the underlying VipsOperation. This is the same as the
     * GObject, just cast to VipsOperation to help FFI.
     *
     * @internal
     */
    public \FFI\CData $pointer;

    function __construct($name)
    {
        Utils::debugLog("VipsOperation", ["name" => $name]);
        $this->pointer = Init::ffi()->vips_operation_new($name);
        if (\FFI::isNull($this->pointer)) {
            Init::error();
        }

        parent::__construct($this->pointer);
    }

    private static function introspect($name) {
        static $cache = [];

        if (!array_key_exists($name, $cache)) {
            $cache[$name] = new Introspect($name);
        }

        return $cache[$name];
    }

    /**
     * Unwrap an array of stuff ready to pass down to the vips_ layer. We
     * swap instances of Image for the ffi pointer.
     *
     * @param array $result Unwrap this.
     *
     * @return array $result unwrapped, ready for vips.
     *
     * @internal
     */
    private static function unwrap(array $result): array
    {
        array_walk_recursive($result, function (&$value) {
            if ($value instanceof Image) {
                $value = $value->image;
            }
        });

        return $result;
    }

    /**
     * Is $value a VipsImage.
     *
     * @param mixed $value The thing to test.
     *
     * @return bool true if this is a ffi VipsImage*.
     *
     * @internal
     */
    private static function isImagePointer($value): bool
    {
        # wat
        # return $value instanceof \FFI::CData &&
        #     \FFI::typeof($value) === Init::ctypes("VipsImage");

        return \FFI::typeof($value) === Init::ctypes("VipsImage");
    }

    /**
     * Wrap up the result of a vips_ call ready to return it to PHP. We do
     * two things:
     *
     * - If the array is a singleton, we strip it off. For example, many
     *   operations return a single result and there's no sense handling
     *   this as an array of values, so we transform ['out' => x] -> x.
     *
     * - Any VipsImage resources are rewrapped as instances of Image.
     *
     * @param mixed $result Wrap this up.
     *
     * @return mixed $result, but wrapped up as a php class.
     *
     * @internal
     */
    private static function wrapResult($result)
    {
        if (!is_array($result)) {
            $result = ['x' => $result];
        }

        array_walk_recursive($result, function (&$item) {
            if (self::isImagePointer($item)) {
                $item = new Image($item);
            }
        });

        if (count($result) === 1) {
            $result = array_shift($result);
        }

        return $result;
    }

    /**
     * Check the result of a vips_ call for an error, and throw an exception
     * if we see one.
     *
     * This won't work for things like __get where a non-array return can be
     * a valid return.
     *
     * @param mixed $result Test this.
     *
     * @throws Exception
     *
     * @return void
     *
     * @internal
     */
    private static function errorIsArray($result): void
    {
        if (!is_array($result)) {
            Init::error();
        }
    }

    /**
     * Call any vips operation. The final element of $arguments can be
     * (but doesn't have to be) an array of options to pass to the operation.
     *
     * We can't have a separate arg for the options since this will be run from
     * __call(), which cannot know which args are required and which are
     * optional. See call() below for a version with the options broken out.
     *
     * @param string     $name      The operation name.
     * @param Image|null $instance  The instance this operation is being invoked
     *      from.
     * @param array      $arguments An array of arguments to pass to the
     *      operation.
     *
     * @throws Exception
     *
     * @return mixed The result(s) of the operation.
     */
    static function callBase(
        string $name,
        ?Image $instance,
        array $arguments
    ) {
        Utils::debugLog($name, [
            'instance' => $instance,
            'arguments' => $arguments
        ]);

        $arguments = array_merge([$name, $instance], $arguments);
        $arguments = array_values(self::unwrap($arguments));
        $operation = new VipsOperation($name);
        $introspect = self::introspect($name);

        Utils::debugLog("callBase", ["setting arguments ..."]);

        if (count($introspect->required_input) != count($arguments)) {
        }


        $operation->set("filename", $filename);

        // build the operation

        Utils::debugLog("callBase", ["building ..."]);
        $new_operation = Init::ffi()->vips_cache_operation_build($operation);
        if (FFI::isNull($new_operation)) {
          Init::ffi()->vips_object_unref_outputs($operation);
          error();
        }
        $operation = $new_operation;

        # need to attach input refs to output

        // fetch required output args

        $result = $operation->get("out");
        self::errorIsArray($result);
        $result = self::wrapResult($result);

        Utils::debugLog($name, ['result' => $result]);

        return $result;
    }

    /**
     * Call any vips operation, with an explicit set of options. This is more
     * convenient than callBase() if you have a set of known options.
     *
     * @param string     $name      The operation name.
     * @param Image|null $instance  The instance this operation is being invoked
     *      from.
     * @param array      $arguments An array of arguments to pass to the
     *      operation.
     * @param array      $options   An array of optional arguments to pass to
     *      the operation.
     *
     * @throws Exception
     *
     * @return mixed The result(s) of the operation.
     */
    public static function call(
        string $name,
        ?Image $instance,
        array $arguments,
        array $options = []
    ) {
        /*
        echo "call: $name \n";
        echo "instance = \n";
        var_dump($instance);
        echo "arguments = \n";
        var_dump($arguments);
        echo "options = \n";
        var_dump($options);
         */

        return self::callBase($name, $instance, 
            array_merge($arguments, [$options]));
    }

    /**
     * Handy for things like self::more. Call a 2-ary vips operator like
     * 'more', but if the arg is not an image (ie. it's a constant), call
     * 'more_const' instead.
     *
     * @param mixed  $other   The right-hand argument.
     * @param string $base    The base part of the operation name.
     * @param string $op      The action to invoke.
     * @param array  $options An array of options to pass to the operation.
     *
     * @throws Exception
     *
     * @return mixed The operation result.
     *
     * @internal
     */
    private function callEnum(
        $other,
        string $base,
        string $op,
        array $options = []
    ) {
        if (self::isImageish($other)) {
            return self::call($base, $this, [$other, $op], $options);
        } else {
            return self::call($base . '_const', $this, [$op, $other], $options);
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
