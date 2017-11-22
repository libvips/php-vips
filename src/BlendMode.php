<?php

/**
 * This file was generated automatically. Do not edit!
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

/**
 * The BlendMode enum.
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/jcupitt/php-vips
 */
abstract class BlendMode
{
    const CLEAR = 'clear';
    const SOURCE = 'source';
    const OVER = 'over';
    const IN = 'in';
    const OUT = 'out';
    const ATOP = 'atop';
    const DEST = 'dest';
    const DEST_OVER = 'dest_over';
    const DEST_IN = 'dest_in';
    const DEST_OUT = 'dest_out';
    const DEST_ATOP = 'dest_atop';
    const XOR = 'xor';
    const ADD = 'add';
    const SATURATE = 'saturate';
    const MULTIPLY = 'multiply';
    const SCREEN = 'screen';
    const OVERLAY = 'overlay';
    const DARKEN = 'darken';
    const LIGHTEN = 'lighten';
    const COLOUR_DOFGE = 'colour_dodge';
    const COLOUR_BURN = 'colour_burn';
    const HARD_LIGHT = 'hard_light';
    const SOFT_LIGHT = 'soft_light';
    const DIFFERENCE = 'difference';
    const EXCLUSION = 'exclusion';

    /* combine takes an array of blend modes, passed to libvips as an array of
     * int. Because libvips does now know they should be enums, we have to do
     * the string->int conversion ourselves. We ought to introspect to find the
     * mapping, but until we have the machinery for that, we just hardwire the
     * mapping here.
     */
    const TO_INT = [
        BlendMode::CLEAR => 0,
        BlendMode::SOURCE => 1,
        BlendMode::OVER => 2,
        BlendMode::IN => 3,
        BlendMode::OUT => 4,
        BlendMode::ATOP => 5,
        BlendMode::DEST => 6,
        BlendMode::DEST_OVER => 7,
        BlendMode::DEST_IN => 8,
        BlendMode::DEST_OUT => 9,
        BlendMode::DEST_ATOP => 10,
        BlendMode::XOR => 11,
        BlendMode::ADD => 12,
        BlendMode::SATURATE => 13,
        BlendMode::MULTIPLY => 14,
        BlendMode::SCREEN => 15,
        BlendMode::OVERLAY => 16,
        BlendMode::DARKEN => 17,
        BlendMode::LIGHTEN => 18,
        BlendMode::COLOUR_DOFGE => 19,
        BlendMode::COLOUR_BURN => 20,
        BlendMode::HARD_LIGHT => 21,
        BlendMode::SOFT_LIGHT => 22,
        BlendMode::DIFFERENCE => 23,
        BlendMode::EXCLUSION => 24
    ];
}
