#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';
use Jcupitt\Vips;

# sigmoidal contrast adjustment in php-vips

# This is a standard contrast adjustment technique: grey values are put through
# an S-shaped curve which boosts the slope in the mid-tones and drops it for
# white and black.

function sigmoid(float $alpha, float $beta, bool $ushort = false): Vips\Image
{
    # make a identity LUT, that is, a lut where each pixel has the value of
    # its index ... if you map an image through the identity, you get the
    # same image back again
    #
    # LUTs in libvips are just images with either the width or height set
    # to 1, and the 'interpretation' tag set to HISTOGRAM
    #
    # if 'ushort' is TRUE, we make a 16-bit LUT, ie. 0 - 65535 values;
    # otherwise it's 8-bit (0 - 255)
    $lut = Vips\Image::identity(['ushort' => $ushort]);

    # rescale so each element is in [0, 1]
    $max = $lut->max();
    $lut = $lut->divide($max);

    # the sigmoidal equation, see
    #
    # http://www.imagemagick.org/Usage/color_mods/#sigmoidal
    #
    # though that's missing a term -- it should be
    #
    # (1/(1+exp(β*(α-u))) - 1/(1+exp(β*α))) /
    #   (1/(1+exp(β*(α-1))) - 1/(1+exp(β*α)))
    #
    # ie. there should be an extra α in the second term
    $x = 1.0 / (1.0 + exp($beta * $alpha));
    $y = 1.0 / (1.0 + exp($beta * ($alpha - 1.0))) - $x;
    $z = $lut->multiply(-1)->add($alpha)->multiply($beta)->exp()->add(1);
    $result = $z->pow(-1)->subtract($x)->divide($y);

    # rescale back to 0 - 255 or 0 - 65535
    $result = $result->multiply($max);

    # and get the format right ... $result will be a float image after all
    # that maths, but we want uchar or ushort
    $result = $result->cast($ushort ?
        Vips\BandFormat::USHORT : Vips\BandFormat::UCHAR);

    return $result;
}

# Apply to RGB. This takes no account of image gamma, and applies the 
# contrast boost to R, G and B bands, thereby also boosting colourfulness. 
function sigRGB(Vips\Image $image, float $alpha, float $beta): Vips\Image
{
    $lut = sigmoid($alpha, $beta, $image->format == Vips\BandFormat::USHORT);

    return $image->maplut($lut);
}

# Fancier: apply to L of CIELAB. This will change luminance equally, and will
# not change colourfulness.
function sigLAB(Vips\Image $image, float $alpha, float $beta): Vips\Image
{
    $old_interpretation = $image->interpretation;

    # labs is CIELAB with colour values expressed as short (signed 16-bit ints)
    # L is in 0 - 32767
    $image = $image->colourspace(Vips\Interpretation::LABS);

    # make a 16-bit LUT, then shrink by x2 to make it fit the range of L in labs
    $lut = sigmoid($alpha, $beta, true);
    $lut = $lut->shrinkh(2)->multiply(0.5);
    $lut = $lut->cast(Vips\BandFormat::SHORT);

    # get the L band from our labs image, map though the LUT, then reattach the
    # ab bands from the labs image
    $L = $image->extract_band(0);
    $AB = $image->extract_band(1, ['n' => 2]);
    $L = $L->maplut($lut);
    $image = $L->bandjoin($AB);

    # and back to our original colourspace again (probably rgb)
    #
    # after the manipulation above, $image will just be tagged as a generic
    # multiband image, vips will no longer know that it's a labs, so we need to
    # tell colourspace what the source space is
    $image = $image->colourspace(
        $old_interpretation,
        ['source_space' => Vips\Interpretation::LABS]
    );

    return $image;
}

$im = Vips\Image::newFromFile($argv[1], ['access' => Vips\Access::SEQUENTIAL]);

# $beta == 10 is a large contrast boost, values below about 4 drop the contrast
#
# sigLAB is the fancy one, and is much slower than sigRGB
$im = sigLAB($im, 0.5, 7);

$im->writeToFile($argv[2]);

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
