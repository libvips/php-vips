#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Jcupitt\Vips;

/**
 * sigmoidal contrast adjustment in php-vips
 *
 * This is a standard contrast adjustment technique: grey values are put through
 * an S-shaped curve which boosts the slope in the mid-tones and drops it for
 * white and black.
 *
 * @param bool $sharpen If true increase the contrast, if false decrease the contrast.
 * @param float $midpoint Midpoint of the contrast (typically 0.5).
 * @param float $contrast Strength of the contrast (typically 3-20).
 * @param bool $ushort Indicating if we have a 16-bit LUT.
 *
 * @return Vips\Image 16-bit or 8-bit LUT
 */
function sigmoid(bool $sharpen, float $midpoint, float $contrast, bool $ushort = false): Vips\Image
{
    /**
     * Make a identity LUT, that is, a lut where each pixel has the value of
     * its index ... if you map an image through the identity, you get the
     * same image back again.
     *
     * LUTs in libvips are just images with either the width or height set
     * to 1, and the 'interpretation' tag set to HISTOGRAM.
     *
     * If 'ushort' is TRUE, we make a 16-bit LUT, ie. 0 - 65535 values;
     * otherwise it's 8-bit (0 - 255)
     */
    $lut = Vips\Image::identity(['ushort' => $ushort]);

    // Rescale so each element is in [0, 1]
    $range = $lut->max();
    $lut = $lut->divide($range);

    /**
     * The sigmoidal equation, see
     *
     * http://www.imagemagick.org/Usage/color_mods/#sigmoidal
     *
     * and
     *
     * http://osdir.com/ml/video.image-magick.devel/2005-04/msg00006.html
     *
     * Though that's missing a term -- it should be
     *
     * (1/(1+exp(β*(α-u))) - 1/(1+exp(β*α))) /
     *   (1/(1+exp(β*(α-1))) - 1/(1+exp(β*α)))
     *
     * ie. there should be an extra α in the second term
     */
    if ($sharpen) {
        $x = $lut->multiply(-1)->add($midpoint)->multiply($contrast)->exp()->add(1)->pow(-1);
        $min = $x->min();
        $max = $x->max();
        $result = $x->subtract($min)->divide($max - $min);
    } else {
        $min = 1 / (1 + exp($contrast * $midpoint));
        $max = 1 / (1 + exp($contrast * ($midpoint - 1)));
        $x = $lut->multiply($max - $min)->add($min);
        $result = $x->multiply(-1)->add(1)->divide($x)->log()->divide($contrast)->multiply(-1)->add($midpoint);
    }

    // Rescale back to 0 - 255 or 0 - 65535
    $result = $result->multiply($range);

    /**
     * And get the format right ... $result will be a float image after all
     * that maths, but we want uchar or ushort
     */
    $result = $result->cast($ushort ? Vips\BandFormat::USHORT : Vips\BandFormat::UCHAR);
    return $result;
}

/**
 * Apply to RGB. This takes no account of image gamma, and applies the
 * contrast boost to R, G and B bands, thereby also boosting colourfulness.
 *
 * @param Vips\Image $image The source image.
 * @param bool $sharpen If true increase the contrast, if false decrease the contrast.
 * @param float $midpoint Midpoint of the contrast (typically 0.5).
 * @param float $contrast Strength of the contrast (typically 3-20).
 *
 * @return Vips\Image The manipulated image.
 */
function sigRGB(Vips\Image $image, bool $sharpen, float $midpoint, float $contrast): Vips\Image
{
    $lut = sigmoid($sharpen, $midpoint, $contrast, $image->format === Vips\BandFormat::USHORT);
    return $image->maplut($lut);
}

/**
 * Fancier: apply to L of CIELAB. This will change luminance equally, and will
 * not change colourfulness.
 *
 * @param Vips\Image $image The source image.
 * @param bool $sharpen If true increase the contrast, if false decrease the contrast.
 * @param float $midpoint Midpoint of the contrast (typically 0.5).
 * @param float $contrast Strength of the contrast (typically 3-20).
 *
 * @return Vips\Image The manipulated image.
 */
function sigLAB(Vips\Image $image, bool $sharpen, float $midpoint, float $contrast): Vips\Image
{
    $oldInterpretation = $image->interpretation;

    /**
     * Labs is CIELAB with colour values expressed as short (signed 16-bit ints)
     * L is in 0 - 32767
     */
    $image = $image->colourspace(Vips\Interpretation::LABS);

    // Make a 16-bit LUT, then shrink by x2 to make it fit the range of L in labs
    $lut = sigmoid($sharpen, $midpoint, $contrast, true);
    $lut = $lut->shrinkh(2)->multiply(0.5);
    $lut = $lut->cast(Vips\BandFormat::SHORT);

    /**
     * Get the L band from our labs image, map though the LUT, then reattach the
     * ab bands from the labs image
     */
    $L = $image->extract_band(0);
    $AB = $image->extract_band(1, ['n' => 2]);
    $L = $L->maplut($lut);
    $image = $L->bandjoin($AB);

    /**
     * And back to our original colourspace again (probably rgb)
     *
     * After the manipulation above, $image will just be tagged as a generic
     * multiband image, vips will no longer know that it's a labs, so we need to
     * tell colourspace what the source space is
     */
    return $image->colourspace($oldInterpretation, [
        'source_space' => Vips\Interpretation::LABS
    ]);
}

if (count($argv) != 3) {
    echo("usage: ./sig.php input-image output-image\n");
    exit(1);
}

$im = Vips\Image::newFromFile($argv[1], ['access' => Vips\Access::SEQUENTIAL]);

/**
 * $contrast == 10 is a large contrast boost, values below about 4 drop the contrast
 *
 * sigLAB is the fancy one, and is much slower than sigRGB
 */
$im = sigLAB($im, true, 0.5, 7);

$im->writeToFile($argv[2]);

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: expandtab sw=4 ts=4 fdm=marker
 * vim<600: expandtab sw=4 ts=4
 */
