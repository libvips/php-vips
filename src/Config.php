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
 * @link      https://github.com/jcupitt/php-vips
 */

namespace Jcupitt\Vips;

use Psr\Log\LoggerInterface;

/**
 * This class contains the top-level libvips control methods.
 *
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/jcupitt/php-vips
 */
class Config
{

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    private static $logger;

    /**
     * Sets a logger. This can be handy for debugging. For example:
     *
     * ```php
     * Vips\Config::setLogger(new Vips\Logger);
     * ```
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    /**
     * Gets a logger.
     *
     * @return LoggerInterface $logger|null
     */
    public static function getLogger()
    {
        return self::$logger;
    }

    /**
     * Set the maximum number of operations to hold in the libvips operation
     * cache.
     *
     * @param integer $value The maximum number of operations to cache.
     *
     * @return void
     */
    public static function cacheSetMax($value)
    {
        vips_cache_set_max($value);
    }

    /**
     * Set the maximum amount of memory to allow cached operations to use, in
     * bytes.
     *
     * @param integer $value The maximum amount of memory cached opertations can
     *     hold, in bytes.
     *
     * @return void
     */
    public static function cacheSetMaxMem($value)
    {
        vips_cache_set_max_mem($value);
    }

    /**
     * Set the maximum number of open files cached operations can use.
     *
     * @param integer $value The maximum number of open files cached operations
     *      can use.
     *
     * @return void
     */
    public static function cacheSetMaxFiles($value)
    {
        vips_cache_set_max_files($value);
    }

    /**
     * Set the size of the pools of worker threads vips uses for image
     * evaluation.
     *
     * @param integer $value The size of the pools of worker threads vips uses
     *      for image evaluation.
     *
     * @return void
     */
    public static function concurrencySet($value)
    {
        vips_concurrency_set($value);
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
