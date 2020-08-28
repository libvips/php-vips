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
 * Autodocs for the Image class.
 * @category  Images
 * @package   Jcupitt\Vips
 * @author    John Cupitt <jcupitt@gmail.com>
 * @copyright 2016 John Cupitt
 * @license   https://opensource.org/licenses/MIT MIT
 * @link      https://github.com/jcupitt/php-vips
 *
 * @method Image CMC2LCh(Image $in, array $options = []) Transform LCh to CMC.
 *     @throws Exception
 * @method Image CMYK2XYZ(Image $in, array $options = []) Transform CMYK to XYZ.
 *     @throws Exception
 * @method Image HSV2sRGB(Image $in, array $options = []) Transform HSV to sRGB.
 *     @throws Exception
 * @method Image LCh2CMC(Image $in, array $options = []) Transform LCh to CMC.
 *     @throws Exception
 * @method Image LCh2Lab(Image $in, array $options = []) Transform LCh to Lab.
 *     @throws Exception
 * @method Image Lab2LCh(Image $in, array $options = []) Transform Lab to LCh.
 *     @throws Exception
 * @method Image Lab2LabQ(Image $in, array $options = []) Transform float Lab to LabQ coding.
 *     @throws Exception
 * @method Image Lab2LabS(Image $in, array $options = []) Transform float Lab to signed short.
 *     @throws Exception
 * @method Image Lab2XYZ(Image $in, array $options = []) Transform CIELAB to XYZ.
 *     @throws Exception
 * @method Image LabQ2Lab(Image $in, array $options = []) Unpack a LabQ image to float Lab.
 *     @throws Exception
 * @method Image LabQ2LabS(Image $in, array $options = []) Unpack a LabQ image to short Lab.
 *     @throws Exception
 * @method Image LabQ2sRGB(Image $in, array $options = []) Convert a LabQ image to sRGB.
 *     @throws Exception
 * @method Image LabS2Lab(Image $in, array $options = []) Transform signed short Lab to float.
 *     @throws Exception
 * @method Image LabS2LabQ(Image $in, array $options = []) Transform short Lab to LabQ coding.
 *     @throws Exception
 * @method Image XYZ2CMYK(Image $in, array $options = []) Transform XYZ to CMYK.
 *     @throws Exception
 * @method Image XYZ2Lab(Image $in, array $options = []) Transform XYZ to Lab.
 *     @throws Exception
 * @method Image XYZ2Yxy(Image $in, array $options = []) Transform XYZ to Yxy.
 *     @throws Exception
 * @method Image XYZ2scRGB(Image $in, array $options = []) Transform XYZ to scRGB.
 *     @throws Exception
 * @method Image Yxy2XYZ(Image $in, array $options = []) Transform Yxy to XYZ.
 *     @throws Exception
 * @method Image abs(Image $in, array $options = []) Absolute value of an image.
 *     @throws Exception
 * @method Image affine(Image $in, float[]|float $matrix, array $options = []) Affine transform of an image.
 *     @throws Exception
 * @method static Image analyzeload(string $filename, array $options = []) Load an Analyze6 image.
 *     @throws Exception
 * @method static Image arrayjoin(Image[]|Image $in, array $options = []) Join an array of images.
 *     @throws Exception
 * @method Image autorot(Image $in, array $options = []) Autorotate image by exif tag.
 *     @throws Exception
 * @method float avg(Image $in, array $options = []) Find image average.
 *     @throws Exception
 * @method Image bandbool(Image $in, string $boolean, array $options = []) Boolean operation across image bands.
 *     @see OperationBoolean for possible values for $boolean
 *     @throws Exception
 * @method Image bandfold(Image $in, array $options = []) Fold up x axis into bands.
 *     @throws Exception
 * @method Image bandjoin_const(Image $in, float[]|float $c, array $options = []) Append a constant band to an image.
 *     @throws Exception
 * @method Image bandmean(Image $in, array $options = []) Band-wise average.
 *     @throws Exception
 * @method Image bandunfold(Image $in, array $options = []) Unfold image bands into x axis.
 *     @throws Exception
 * @method static Image black(integer $width, integer $height, array $options = []) Make a black image.
 *     @throws Exception
 * @method Image boolean(Image $left, Image $right, string $boolean, array $options = []) Boolean operation on two images.
 *     @see OperationBoolean for possible values for $boolean
 *     @throws Exception
 * @method Image boolean_const(Image $in, string $boolean, float[]|float $c, array $options = []) Boolean operations against a constant.
 *     @see OperationBoolean for possible values for $boolean
 *     @throws Exception
 * @method Image buildlut(Image $in, array $options = []) Build a look-up table.
 *     @throws Exception
 * @method Image byteswap(Image $in, array $options = []) Byteswap an image.
 *     @throws Exception
 * @method Image cache(Image $in, array $options = []) Cache an image.
 *     @throws Exception
 * @method Image canny(Image $in, array $options = []) Canny edge detector.
 *     @throws Exception
 * @method Image case(Image $index, Image[]|Image $cases, array $options = []) Use pixel values to pick cases from an array of images.
 *     @throws Exception
 * @method Image cast(Image $in, string $format, array $options = []) Cast an image.
 *     @see BandFormat for possible values for $format
 *     @throws Exception
 * @method Image colourspace(Image $in, string $space, array $options = []) Convert to a new colorspace.
 *     @see Interpretation for possible values for $space
 *     @throws Exception
 * @method Image compass(Image $in, Image $mask, array $options = []) Convolve with rotating mask.
 *     @throws Exception
 * @method Image complex(Image $in, string $cmplx, array $options = []) Perform a complex operation on an image.
 *     @see OperationComplex for possible values for $cmplx
 *     @throws Exception
 * @method Image complex2(Image $left, Image $right, string $cmplx, array $options = []) Complex binary operations on two images.
 *     @see OperationComplex2 for possible values for $cmplx
 *     @throws Exception
 * @method Image complexform(Image $left, Image $right, array $options = []) Form a complex image from two real images.
 *     @throws Exception
 * @method Image complexget(Image $in, string $get, array $options = []) Get a component from a complex image.
 *     @see OperationComplexget for possible values for $get
 *     @throws Exception
 * @method static Image composite(Image[]|Image $in, integer[]|integer $mode, array $options = []) Blend an array of images with an array of blend modes.
 *     @throws Exception
 * @method Image composite2(Image $base, Image $overlay, string $mode, array $options = []) Blend a pair of images with a blend mode.
 *     @see BlendMode for possible values for $mode
 *     @throws Exception
 * @method Image conv(Image $in, Image $mask, array $options = []) Convolution operation.
 *     @throws Exception
 * @method Image conva(Image $in, Image $mask, array $options = []) Approximate integer convolution.
 *     @throws Exception
 * @method Image convasep(Image $in, Image $mask, array $options = []) Approximate separable integer convolution.
 *     @throws Exception
 * @method Image convf(Image $in, Image $mask, array $options = []) Float convolution operation.
 *     @throws Exception
 * @method Image convi(Image $in, Image $mask, array $options = []) Int convolution operation.
 *     @throws Exception
 * @method Image convsep(Image $in, Image $mask, array $options = []) Seperable convolution operation.
 *     @throws Exception
 * @method Image copy(Image $in, array $options = []) Copy an image.
 *     @throws Exception
 * @method float countlines(Image $in, string $direction, array $options = []) Count lines in an image.
 *     @see Direction for possible values for $direction
 *     @throws Exception
 * @method Image crop(Image $input, integer $left, integer $top, integer $width, integer $height, array $options = []) Extract an area from an image.
 *     @throws Exception
 * @method static Image csvload(string $filename, array $options = []) Load csv.
 *     @throws Exception
 * @method static Image csvload_source(string $source, array $options = []) Load csv.
 *     @throws Exception
 * @method void csvsave(Image $in, string $filename, array $options = []) Save image to csv.
 *     @throws Exception
 * @method void csvsave_target(Image $in, string $target, array $options = []) Save image to csv.
 *     @throws Exception
 * @method Image dE00(Image $left, Image $right, array $options = []) Calculate dE00.
 *     @throws Exception
 * @method Image dE76(Image $left, Image $right, array $options = []) Calculate dE76.
 *     @throws Exception
 * @method Image dECMC(Image $left, Image $right, array $options = []) Calculate dECMC.
 *     @throws Exception
 * @method float deviate(Image $in, array $options = []) Find image standard deviation.
 *     @throws Exception
 * @method Image draw_circle(Image $image, float[]|float $ink, integer $cx, integer $cy, integer $radius, array $options = []) Draw a circle on an image.
 *     @throws Exception
 * @method Image draw_flood(Image $image, float[]|float $ink, integer $x, integer $y, array $options = []) Flood-fill an area.
 *     @throws Exception
 * @method Image draw_image(Image $image, Image $sub, integer $x, integer $y, array $options = []) Paint an image into another image.
 *     @throws Exception
 * @method Image draw_line(Image $image, float[]|float $ink, integer $x1, integer $y1, integer $x2, integer $y2, array $options = []) Draw a line on an image.
 *     @throws Exception
 * @method Image draw_mask(Image $image, float[]|float $ink, Image $mask, integer $x, integer $y, array $options = []) Draw a mask on an image.
 *     @throws Exception
 * @method Image draw_rect(Image $image, float[]|float $ink, integer $left, integer $top, integer $width, integer $height, array $options = []) Paint a rectangle on an image.
 *     @throws Exception
 * @method Image draw_smudge(Image $image, integer $left, integer $top, integer $width, integer $height, array $options = []) Blur a rectangle on an image.
 *     @throws Exception
 * @method void dzsave(Image $in, string $filename, array $options = []) Save image to deepzoom file.
 *     @throws Exception
 * @method string dzsave_buffer(Image $in, array $options = []) Save image to dz buffer.
 *     @throws Exception
 * @method Image embed(Image $in, integer $x, integer $y, integer $width, integer $height, array $options = []) Embed an image in a larger image.
 *     @throws Exception
 * @method Image extract_area(Image $input, integer $left, integer $top, integer $width, integer $height, array $options = []) Extract an area from an image.
 *     @throws Exception
 * @method Image extract_band(Image $in, integer $band, array $options = []) Extract band from an image.
 *     @throws Exception
 * @method static Image eye(integer $width, integer $height, array $options = []) Make an image showing the eye's spatial response.
 *     @throws Exception
 * @method Image falsecolour(Image $in, array $options = []) False-color an image.
 *     @throws Exception
 * @method Image fastcor(Image $in, Image $ref, array $options = []) Fast correlation.
 *     @throws Exception
 * @method Image fill_nearest(Image $in, array $options = []) Fill image zeros with nearest non-zero pixel.
 *     @throws Exception
 * @method array find_trim(Image $in, array $options = []) Search an image for non-edge areas.
 *     Return array with: [
 *         'left' => @type integer Left edge of image
 *         'top' => @type integer Top edge of extract area
 *         'width' => @type integer Width of extract area
 *         'height' => @type integer Height of extract area
 *     ];
 *     @throws Exception
 * @method static Image fitsload(string $filename, array $options = []) Load a FITS image.
 *     @throws Exception
 * @method void fitssave(Image $in, string $filename, array $options = []) Save image to fits file.
 *     @throws Exception
 * @method Image flatten(Image $in, array $options = []) Flatten alpha out of an image.
 *     @throws Exception
 * @method Image flip(Image $in, string $direction, array $options = []) Flip an image.
 *     @see Direction for possible values for $direction
 *     @throws Exception
 * @method Image float2rad(Image $in, array $options = []) Transform float RGB to Radiance coding.
 *     @throws Exception
 * @method static Image fractsurf(integer $width, integer $height, float $fractal_dimension, array $options = []) Make a fractal surface.
 *     @throws Exception
 * @method Image freqmult(Image $in, Image $mask, array $options = []) Frequency-domain filtering.
 *     @throws Exception
 * @method Image fwfft(Image $in, array $options = []) Forward FFT.
 *     @throws Exception
 * @method Image gamma(Image $in, array $options = []) Gamma an image.
 *     @throws Exception
 * @method Image gaussblur(Image $in, float $sigma, array $options = []) Gaussian blur.
 *     @throws Exception
 * @method static Image gaussmat(float $sigma, float $min_ampl, array $options = []) Make a gaussian image.
 *     @throws Exception
 * @method static Image gaussnoise(integer $width, integer $height, array $options = []) Make a gaussnoise image.
 *     @throws Exception
 * @method array getpoint(Image $in, integer $x, integer $y, array $options = []) Read a point from an image.
 *     @throws Exception
 * @method static Image gifload(string $filename, array $options = []) Load GIF with giflib.
 *     @throws Exception
 * @method static Image gifload_buffer(string $buffer, array $options = []) Load GIF with giflib.
 *     @throws Exception
 * @method static Image gifload_source(string $source, array $options = []) Load GIF with giflib.
 *     @throws Exception
 * @method Image globalbalance(Image $in, array $options = []) Global balance an image mosaic.
 *     @throws Exception
 * @method Image gravity(Image $in, string $direction, integer $width, integer $height, array $options = []) Place an image within a larger image with a certain gravity.
 *     @see CompassDirection for possible values for $direction
 *     @throws Exception
 * @method static Image grey(integer $width, integer $height, array $options = []) Make a grey ramp image.
 *     @throws Exception
 * @method Image grid(Image $in, integer $tile_height, integer $across, integer $down, array $options = []) Grid an image.
 *     @throws Exception
 * @method static Image heifload(string $filename, array $options = []) Load a HEIF image.
 *     @throws Exception
 * @method static Image heifload_buffer(string $buffer, array $options = []) Load a HEIF image.
 *     @throws Exception
 * @method static Image heifload_source(string $source, array $options = []) Load a HEIF image.
 *     @throws Exception
 * @method void heifsave(Image $in, string $filename, array $options = []) Save image in HEIF format.
 *     @throws Exception
 * @method string heifsave_buffer(Image $in, array $options = []) Save image in HEIF format.
 *     @throws Exception
 * @method void heifsave_target(Image $in, string $target, array $options = []) Save image in HEIF format.
 *     @throws Exception
 * @method Image hist_cum(Image $in, array $options = []) Form cumulative histogram.
 *     @throws Exception
 * @method float hist_entropy(Image $in, array $options = []) Estimate image entropy.
 *     @throws Exception
 * @method Image hist_equal(Image $in, array $options = []) Histogram equalisation.
 *     @throws Exception
 * @method Image hist_find(Image $in, array $options = []) Find image histogram.
 *     @throws Exception
 * @method Image hist_find_indexed(Image $in, Image $index, array $options = []) Find indexed image histogram.
 *     @throws Exception
 * @method Image hist_find_ndim(Image $in, array $options = []) Find n-dimensional image histogram.
 *     @throws Exception
 * @method bool hist_ismonotonic(Image $in, array $options = []) Test for monotonicity.
 *     @throws Exception
 * @method Image hist_local(Image $in, integer $width, integer $height, array $options = []) Local histogram equalisation.
 *     @throws Exception
 * @method Image hist_match(Image $in, Image $ref, array $options = []) Match two histograms.
 *     @throws Exception
 * @method Image hist_norm(Image $in, array $options = []) Normalise histogram.
 *     @throws Exception
 * @method Image hist_plot(Image $in, array $options = []) Plot histogram.
 *     @throws Exception
 * @method Image hough_circle(Image $in, array $options = []) Find hough circle transform.
 *     @throws Exception
 * @method Image hough_line(Image $in, array $options = []) Find hough line transform.
 *     @throws Exception
 * @method Image icc_export(Image $in, array $options = []) Output to device with ICC profile.
 *     @throws Exception
 * @method Image icc_import(Image $in, array $options = []) Import from device with ICC profile.
 *     @throws Exception
 * @method Image icc_transform(Image $in, string $output_profile, array $options = []) Transform between devices with ICC profiles.
 *     @throws Exception
 * @method static Image identity(array $options = []) Make a 1D image where pixel values are indexes.
 *     @throws Exception
 * @method Image insert(Image $main, Image $sub, integer $x, integer $y, array $options = []) Insert image @sub into @main at @x, @y.
 *     @throws Exception
 * @method Image invert(Image $in, array $options = []) Invert an image.
 *     @throws Exception
 * @method Image invertlut(Image $in, array $options = []) Build an inverted look-up table.
 *     @throws Exception
 * @method Image invfft(Image $in, array $options = []) Inverse FFT.
 *     @throws Exception
 * @method Image join(Image $in1, Image $in2, string $direction, array $options = []) Join a pair of images.
 *     @see Direction for possible values for $direction
 *     @throws Exception
 * @method static Image jpegload(string $filename, array $options = []) Load jpeg from file.
 *     @throws Exception
 * @method static Image jpegload_buffer(string $buffer, array $options = []) Load jpeg from buffer.
 *     @throws Exception
 * @method static Image jpegload_source(string $source, array $options = []) Load image from jpeg source.
 *     @throws Exception
 * @method void jpegsave(Image $in, string $filename, array $options = []) Save image to jpeg file.
 *     @throws Exception
 * @method string jpegsave_buffer(Image $in, array $options = []) Save image to jpeg buffer.
 *     @throws Exception
 * @method void jpegsave_mime(Image $in, array $options = []) Save image to jpeg mime.
 *     @throws Exception
 * @method void jpegsave_target(Image $in, string $target, array $options = []) Save image to jpeg target.
 *     @throws Exception
 * @method Image labelregions(Image $in, array $options = []) Label regions in an image.
 *     @throws Exception
 * @method Image linear(Image $in, float[]|float $a, float[]|float $b, array $options = []) Calculate (a * in + b).
 *     @throws Exception
 * @method Image linecache(Image $in, array $options = []) Cache an image as a set of lines.
 *     @throws Exception
 * @method static Image logmat(float $sigma, float $min_ampl, array $options = []) Make a laplacian of gaussian image.
 *     @throws Exception
 * @method static Image magickload(string $filename, array $options = []) Load file with ImageMagick.
 *     @throws Exception
 * @method static Image magickload_buffer(string $buffer, array $options = []) Load buffer with ImageMagick.
 *     @throws Exception
 * @method void magicksave(Image $in, string $filename, array $options = []) Save file with ImageMagick.
 *     @throws Exception
 * @method string magicksave_buffer(Image $in, array $options = []) Save image to magick buffer.
 *     @throws Exception
 * @method Image mapim(Image $in, Image $index, array $options = []) Resample with a map image.
 *     @throws Exception
 * @method Image maplut(Image $in, Image $lut, array $options = []) Map an image though a lut.
 *     @throws Exception
 * @method static Image mask_butterworth(integer $width, integer $height, float $order, float $frequency_cutoff, float $amplitude_cutoff, array $options = []) Make a butterworth filter.
 *     @throws Exception
 * @method static Image mask_butterworth_band(integer $width, integer $height, float $order, float $frequency_cutoff_x, float $frequency_cutoff_y, float $radius, float $amplitude_cutoff, array $options = []) Make a butterworth_band filter.
 *     @throws Exception
 * @method static Image mask_butterworth_ring(integer $width, integer $height, float $order, float $frequency_cutoff, float $amplitude_cutoff, float $ringwidth, array $options = []) Make a butterworth ring filter.
 *     @throws Exception
 * @method static Image mask_fractal(integer $width, integer $height, float $fractal_dimension, array $options = []) Make fractal filter.
 *     @throws Exception
 * @method static Image mask_gaussian(integer $width, integer $height, float $frequency_cutoff, float $amplitude_cutoff, array $options = []) Make a gaussian filter.
 *     @throws Exception
 * @method static Image mask_gaussian_band(integer $width, integer $height, float $frequency_cutoff_x, float $frequency_cutoff_y, float $radius, float $amplitude_cutoff, array $options = []) Make a gaussian filter.
 *     @throws Exception
 * @method static Image mask_gaussian_ring(integer $width, integer $height, float $frequency_cutoff, float $amplitude_cutoff, float $ringwidth, array $options = []) Make a gaussian ring filter.
 *     @throws Exception
 * @method static Image mask_ideal(integer $width, integer $height, float $frequency_cutoff, array $options = []) Make an ideal filter.
 *     @throws Exception
 * @method static Image mask_ideal_band(integer $width, integer $height, float $frequency_cutoff_x, float $frequency_cutoff_y, float $radius, array $options = []) Make an ideal band filter.
 *     @throws Exception
 * @method static Image mask_ideal_ring(integer $width, integer $height, float $frequency_cutoff, float $ringwidth, array $options = []) Make an ideal ring filter.
 *     @throws Exception
 * @method Image match(Image $ref, Image $sec, integer $xr1, integer $yr1, integer $xs1, integer $ys1, integer $xr2, integer $yr2, integer $xs2, integer $ys2, array $options = []) First-order match of two images.
 *     @throws Exception
 * @method Image math(Image $in, string $math, array $options = []) Apply a math operation to an image.
 *     @see OperationMath for possible values for $math
 *     @throws Exception
 * @method Image math2(Image $left, Image $right, string $math2, array $options = []) Binary math operations.
 *     @see OperationMath2 for possible values for $math2
 *     @throws Exception
 * @method Image math2_const(Image $in, string $math2, float[]|float $c, array $options = []) Binary math operations with a constant.
 *     @see OperationMath2 for possible values for $math2
 *     @throws Exception
 * @method static Image matload(string $filename, array $options = []) Load mat from file.
 *     @throws Exception
 * @method Image matrixinvert(Image $in, array $options = []) Invert an matrix.
 *     @throws Exception
 * @method static Image matrixload(string $filename, array $options = []) Load matrix.
 *     @throws Exception
 * @method static Image matrixload_source(string $source, array $options = []) Load matrix.
 *     @throws Exception
 * @method void matrixprint(Image $in, array $options = []) Print matrix.
 *     @throws Exception
 * @method void matrixsave(Image $in, string $filename, array $options = []) Save image to matrix.
 *     @throws Exception
 * @method void matrixsave_target(Image $in, string $target, array $options = []) Save image to matrix.
 *     @throws Exception
 * @method float max(Image $in, array $options = []) Find image maximum.
 *     @throws Exception
 * @method Image measure(Image $in, integer $h, integer $v, array $options = []) Measure a set of patches on a color chart.
 *     @throws Exception
 * @method Image merge(Image $ref, Image $sec, string $direction, integer $dx, integer $dy, array $options = []) Merge two images.
 *     @see Direction for possible values for $direction
 *     @throws Exception
 * @method float min(Image $in, array $options = []) Find image minimum.
 *     @throws Exception
 * @method Image morph(Image $in, Image $mask, string $morph, array $options = []) Morphology operation.
 *     @see OperationMorphology for possible values for $morph
 *     @throws Exception
 * @method Image mosaic(Image $ref, Image $sec, string $direction, integer $xref, integer $yref, integer $xsec, integer $ysec, array $options = []) Mosaic two images.
 *     @see Direction for possible values for $direction
 *     @throws Exception
 * @method Image mosaic1(Image $ref, Image $sec, string $direction, integer $xr1, integer $yr1, integer $xs1, integer $ys1, integer $xr2, integer $yr2, integer $xs2, integer $ys2, array $options = []) First-order mosaic of two images.
 *     @see Direction for possible values for $direction
 *     @throws Exception
 * @method Image msb(Image $in, array $options = []) Pick most-significant byte from an image.
 *     @throws Exception
 * @method static Image niftiload(string $filename, array $options = []) Load a NIFTI image.
 *     @throws Exception
 * @method void niftisave(Image $in, string $filename, array $options = []) Save image to nifti file.
 *     @throws Exception
 * @method static Image openexrload(string $filename, array $options = []) Load an OpenEXR image.
 *     @throws Exception
 * @method static Image openslideload(string $filename, array $options = []) Load file with OpenSlide.
 *     @throws Exception
 * @method static Image pdfload(string $filename, array $options = []) Load PDF from file.
 *     @throws Exception
 * @method static Image pdfload_buffer(string $buffer, array $options = []) Load PDF from buffer.
 *     @throws Exception
 * @method static Image pdfload_source(string $source, array $options = []) Load PDF from source.
 *     @throws Exception
 * @method integer percent(Image $in, float $percent, array $options = []) Find threshold for percent of pixels.
 *     @throws Exception
 * @method static Image perlin(integer $width, integer $height, array $options = []) Make a perlin noise image.
 *     @throws Exception
 * @method Image phasecor(Image $in, Image $in2, array $options = []) Calculate phase correlation.
 *     @throws Exception
 * @method static Image pngload(string $filename, array $options = []) Load png from file.
 *     @throws Exception
 * @method static Image pngload_buffer(string $buffer, array $options = []) Load png from buffer.
 *     @throws Exception
 * @method static Image pngload_source(string $source, array $options = []) Load png from source.
 *     @throws Exception
 * @method void pngsave(Image $in, string $filename, array $options = []) Save image to png file.
 *     @throws Exception
 * @method string pngsave_buffer(Image $in, array $options = []) Save image to png buffer.
 *     @throws Exception
 * @method void pngsave_target(Image $in, string $target, array $options = []) Save image to target as PNG.
 *     @throws Exception
 * @method static Image ppmload(string $filename, array $options = []) Load ppm from file.
 *     @throws Exception
 * @method static Image ppmload_source(string $source, array $options = []) Load ppm base class.
 *     @throws Exception
 * @method void ppmsave(Image $in, string $filename, array $options = []) Save image to ppm file.
 *     @throws Exception
 * @method void ppmsave_target(Image $in, string $target, array $options = []) Save to ppm.
 *     @throws Exception
 * @method Image premultiply(Image $in, array $options = []) Premultiply image alpha.
 *     @throws Exception
 * @method array profile(Image $in, array $options = []) Find image profiles.
 *     Return array with: [
 *         'columns' => @type Image First non-zero pixel in column
 *         'rows' => @type Image First non-zero pixel in row
 *     ];
 *     @throws Exception
 * @method static string profile_load(string $name, array $options = []) Load named ICC profile.
 *     @throws Exception
 * @method array project(Image $in, array $options = []) Find image projections.
 *     Return array with: [
 *         'columns' => @type Image Sums of columns
 *         'rows' => @type Image Sums of rows
 *     ];
 *     @throws Exception
 * @method Image quadratic(Image $in, Image $coeff, array $options = []) Resample an image with a quadratic transform.
 *     @throws Exception
 * @method Image rad2float(Image $in, array $options = []) Unpack Radiance coding to float RGB.
 *     @throws Exception
 * @method static Image radload(string $filename, array $options = []) Load a Radiance image from a file.
 *     @throws Exception
 * @method static Image radload_buffer(string $buffer, array $options = []) Load rad from buffer.
 *     @throws Exception
 * @method static Image radload_source(string $source, array $options = []) Load rad from source.
 *     @throws Exception
 * @method void radsave(Image $in, string $filename, array $options = []) Save image to Radiance file.
 *     @throws Exception
 * @method string radsave_buffer(Image $in, array $options = []) Save image to Radiance buffer.
 *     @throws Exception
 * @method void radsave_target(Image $in, string $target, array $options = []) Save image to Radiance target.
 *     @throws Exception
 * @method Image rank(Image $in, integer $width, integer $height, integer $index, array $options = []) Rank filter.
 *     @throws Exception
 * @method static Image rawload(string $filename, integer $width, integer $height, integer $bands, array $options = []) Load raw data from a file.
 *     @throws Exception
 * @method void rawsave(Image $in, string $filename, array $options = []) Save image to raw file.
 *     @throws Exception
 * @method void rawsave_fd(Image $in, integer $fd, array $options = []) Write raw image to file descriptor.
 *     @throws Exception
 * @method Image recomb(Image $in, Image $m, array $options = []) Linear recombination with matrix.
 *     @throws Exception
 * @method Image reduce(Image $in, float $hshrink, float $vshrink, array $options = []) Reduce an image.
 *     @throws Exception
 * @method Image reduceh(Image $in, float $hshrink, array $options = []) Shrink an image horizontally.
 *     @throws Exception
 * @method Image reducev(Image $in, float $vshrink, array $options = []) Shrink an image vertically.
 *     @throws Exception
 * @method Image relational(Image $left, Image $right, string $relational, array $options = []) Relational operation on two images.
 *     @see OperationRelational for possible values for $relational
 *     @throws Exception
 * @method Image relational_const(Image $in, string $relational, float[]|float $c, array $options = []) Relational operations against a constant.
 *     @see OperationRelational for possible values for $relational
 *     @throws Exception
 * @method Image remainder_const(Image $in, float[]|float $c, array $options = []) Remainder after integer division of an image and a constant.
 *     @throws Exception
 * @method Image replicate(Image $in, integer $across, integer $down, array $options = []) Replicate an image.
 *     @throws Exception
 * @method Image resize(Image $in, float $scale, array $options = []) Resize an image.
 *     @throws Exception
 * @method Image rot(Image $in, string $angle, array $options = []) Rotate an image.
 *     @see Angle for possible values for $angle
 *     @throws Exception
 * @method Image rot45(Image $in, array $options = []) Rotate an image.
 *     @throws Exception
 * @method Image rotate(Image $in, float $angle, array $options = []) Rotate an image by a number of degrees.
 *     @throws Exception
 * @method Image round(Image $in, string $round, array $options = []) Perform a round function on an image.
 *     @see OperationRound for possible values for $round
 *     @throws Exception
 * @method Image sRGB2HSV(Image $in, array $options = []) Transform sRGB to HSV.
 *     @throws Exception
 * @method Image sRGB2scRGB(Image $in, array $options = []) Convert an sRGB image to scRGB.
 *     @throws Exception
 * @method Image scRGB2BW(Image $in, array $options = []) Convert scRGB to BW.
 *     @throws Exception
 * @method Image scRGB2XYZ(Image $in, array $options = []) Transform scRGB to XYZ.
 *     @throws Exception
 * @method Image scRGB2sRGB(Image $in, array $options = []) Convert an scRGB image to sRGB.
 *     @throws Exception
 * @method Image scale(Image $in, array $options = []) Scale an image to uchar.
 *     @throws Exception
 * @method Image sequential(Image $in, array $options = []) Check sequential access.
 *     @throws Exception
 * @method Image sharpen(Image $in, array $options = []) Unsharp masking for print.
 *     @throws Exception
 * @method Image shrink(Image $in, float $hshrink, float $vshrink, array $options = []) Shrink an image.
 *     @throws Exception
 * @method Image shrinkh(Image $in, integer $hshrink, array $options = []) Shrink an image horizontally.
 *     @throws Exception
 * @method Image shrinkv(Image $in, integer $vshrink, array $options = []) Shrink an image vertically.
 *     @throws Exception
 * @method Image sign(Image $in, array $options = []) Unit vector of pixel.
 *     @throws Exception
 * @method Image similarity(Image $in, array $options = []) Similarity transform of an image.
 *     @throws Exception
 * @method static Image sines(integer $width, integer $height, array $options = []) Make a 2D sine wave.
 *     @throws Exception
 * @method Image smartcrop(Image $input, integer $width, integer $height, array $options = []) Extract an area from an image.
 *     @throws Exception
 * @method Image sobel(Image $in, array $options = []) Sobel edge detector.
 *     @throws Exception
 * @method Image spcor(Image $in, Image $ref, array $options = []) Spatial correlation.
 *     @throws Exception
 * @method Image spectrum(Image $in, array $options = []) Make displayable power spectrum.
 *     @throws Exception
 * @method Image stats(Image $in, array $options = []) Find many image stats.
 *     @throws Exception
 * @method Image stdif(Image $in, integer $width, integer $height, array $options = []) Statistical difference.
 *     @throws Exception
 * @method Image subsample(Image $input, integer $xfac, integer $yfac, array $options = []) Subsample an image.
 *     @throws Exception
 * @method static Image sum(Image[]|Image $in, array $options = []) Sum an array of images.
 *     @throws Exception
 * @method static Image svgload(string $filename, array $options = []) Load SVG with rsvg.
 *     @throws Exception
 * @method static Image svgload_buffer(string $buffer, array $options = []) Load SVG with rsvg.
 *     @throws Exception
 * @method static Image svgload_source(string $source, array $options = []) Load svg from source.
 *     @throws Exception
 * @method static Image switch(Image[]|Image $tests, array $options = []) Find the index of the first non-zero pixel in tests.
 *     @throws Exception
 * @method static void system(string $cmd_format, array $options = []) Run an external command.
 *     @throws Exception
 * @method static Image text(string $text, array $options = []) Make a text image.
 *     @throws Exception
 * @method static Image thumbnail(string $filename, integer $width, array $options = []) Generate thumbnail from file.
 *     @throws Exception
 * @method static Image thumbnail_buffer(string $buffer, integer $width, array $options = []) Generate thumbnail from buffer.
 *     @throws Exception
 * @method Image thumbnail_image(Image $in, integer $width, array $options = []) Generate thumbnail from image.
 *     @throws Exception
 * @method static Image thumbnail_source(string $source, integer $width, array $options = []) Generate thumbnail from source.
 *     @throws Exception
 * @method static Image tiffload(string $filename, array $options = []) Load tiff from file.
 *     @throws Exception
 * @method static Image tiffload_buffer(string $buffer, array $options = []) Load tiff from buffer.
 *     @throws Exception
 * @method static Image tiffload_source(string $source, array $options = []) Load tiff from source.
 *     @throws Exception
 * @method void tiffsave(Image $in, string $filename, array $options = []) Save image to tiff file.
 *     @throws Exception
 * @method string tiffsave_buffer(Image $in, array $options = []) Save image to tiff buffer.
 *     @throws Exception
 * @method Image tilecache(Image $in, array $options = []) Cache an image as a set of tiles.
 *     @throws Exception
 * @method static Image tonelut(array $options = []) Build a look-up table.
 *     @throws Exception
 * @method Image transpose3d(Image $in, array $options = []) Transpose3d an image.
 *     @throws Exception
 * @method Image unpremultiply(Image $in, array $options = []) Unpremultiply image alpha.
 *     @throws Exception
 * @method static Image vipsload(string $filename, array $options = []) Load vips from file.
 *     @throws Exception
 * @method void vipssave(Image $in, string $filename, array $options = []) Save image to vips file.
 *     @throws Exception
 * @method static Image webpload(string $filename, array $options = []) Load webp from file.
 *     @throws Exception
 * @method static Image webpload_buffer(string $buffer, array $options = []) Load webp from buffer.
 *     @throws Exception
 * @method static Image webpload_source(string $source, array $options = []) Load webp from source.
 *     @throws Exception
 * @method void webpsave(Image $in, string $filename, array $options = []) Save image to webp file.
 *     @throws Exception
 * @method string webpsave_buffer(Image $in, array $options = []) Save image to webp buffer.
 *     @throws Exception
 * @method void webpsave_target(Image $in, string $target, array $options = []) Save image to webp target.
 *     @throws Exception
 * @method static Image worley(integer $width, integer $height, array $options = []) Make a worley noise image.
 *     @throws Exception
 * @method Image wrap(Image $in, array $options = []) Wrap image origin.
 *     @throws Exception
 * @method static Image xyz(integer $width, integer $height, array $options = []) Make an image where pixel values are coordinates.
 *     @throws Exception
 * @method static Image zone(integer $width, integer $height, array $options = []) Make a zone plate.
 *     @throws Exception
 * @method Image zoom(Image $input, integer $xfac, integer $yfac, array $options = []) Zoom an image.
 *     @throws Exception
 *
 * @property integer $width Image width in pixels
 * @property integer $height Image height in pixels
 * @property integer $bands Number of bands in image
 * @property string $format Pixel format in image
 *     @see BandFormat for possible values
 * @property string $coding Pixel coding
 *     @see Coding for possible values
 * @property string $interpretation Pixel interpretation
 *     @see Interpretation for possible values
 * @property integer $xoffset Horizontal offset of origin
 * @property integer $yoffset Vertical offset of origin
 * @property float $xres Horizontal resolution in pixels/mm
 * @property float $yres Vertical resolution in pixels/mm
 * @property string $filename Image filename
 */
abstract class ImageAutodoc
{
}
