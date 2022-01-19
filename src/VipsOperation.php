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
abstract class VipsOperation extends VipsObject
{
    /**
     * A pointer to the underlying VipsOperation. This is the same as the
     * GObject, just cast to VipsOperation to help FFI.
     *
     * @internal
     */
    private FFI\CData $vipsOperation;

    /**
     * Cache introsection results here.
     */
    private static $introspectionCache[] mixed = [];

    function __construct($pointer)
    {
        global $ffi;
        global $ctypes;

        $this->vipsOperation = $ffi->cast($ctypes["VipsOperation"], $pointer);
        parent::__construct($pointer);
    }

    function getIntrospection($name) {
        if (!array_key_exists($introspectionCache, $name)) {
            $introspectionCache[$name] = new VipsIntrospection($name);
        }

        return $introspectionCache[$name];
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
    function callBase(
        string $name,
        ?Image $instance,
        array $arguments
    ) {
        Utils::debugLog($name, [
            'instance' => $instance,
            'arguments' => $arguments
        ]);

        $introspection = $this->getIntrospection($name);

        $arguments = array_merge([$name, $instance], $arguments);

        $arguments = array_values(self::unwrap($arguments));
        $result = vips_call(...$arguments);
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



// now use the info from introspection to set some parameters on $operation

trace("setting arguments ...");
gobject_set($operation, "filename", $filename);

// build the operation

trace("building ...");
$new_operation = $ffi->vips_cache_operation_build($operation);
if (FFI::isNull($new_operation)) {
  $ffi->vips_object_unref_outputs($operation);
  error();
}
$operation = $new_operation;

# need to attach input refs to output

// fetch required output args

$image = gobject_get($operation, "out");
trace("result: " . print_r($image, true));



}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
