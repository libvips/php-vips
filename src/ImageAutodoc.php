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
 * @method static void system(string $cmd_format, array $options = []) Run an external command.
 * @method Image relational(Image $right, string $relational, array $options = []) Relational operation on two images.
 *     @see OperationRelational for possible values for $relational
 * @method Image boolean(Image $right, string $boolean, array $options = []) Boolean operation on two images.
 *     @see OperationBoolean for possible values for $boolean
 * @method Image math2(Image $right, string $math2, array $options = []) Binary math operations.
 *     @see OperationMath2 for possible values for $math2
 * @method Image complex2(Image $right, string $cmplx, array $options = []) Complex binary operations on two images.
 *     @see OperationComplex2 for possible values for $cmplx
 * @method Image complexform(Image $right, array $options = []) Form a complex image from two real images.
 * @method static Image sum(Image[] $in, array $options = []) Sum an array of images.
 * @method Image invert(array $options = []) Invert an image.
 * @method Image linear(float[]|float $a, float[]|float $b, array $options = []) Calculate (a * in + b).
 * @method Image math(string $math, array $options = []) Apply a math operation to an image.
 *     @see OperationMath for possible values for $math
 * @method Image abs(array $options = []) Absolute value of an image.
 * @method Image sign(array $options = []) Unit vector of pixel.
 * @method Image round(string $round, array $options = []) Perform a round function on an image.
 *     @see OperationRound for possible values for $round
 * @method Image relational_const(string $relational, float[]|float $c, array $options = []) Relational operations against a constant.
 *     @see OperationRelational for possible values for $relational
 * @method Image remainder_const(float[]|float $c, array $options = []) Remainder after integer division of an image and a constant.
 * @method Image boolean_const(string $boolean, float[]|float $c, array $options = []) Boolean operations against a constant.
 *     @see OperationBoolean for possible values for $boolean
 * @method Image math2_const(string $math2, float[]|float $c, array $options = []) Binary math operations with a constant.
 *     @see OperationMath2 for possible values for $math2
 * @method Image complex(string $cmplx, array $options = []) Perform a complex operation on an image.
 *     @see OperationComplex for possible values for $cmplx
 * @method Image complexget(string $get, array $options = []) Get a component from a complex image.
 *     @see OperationComplexget for possible values for $get
 * @method float avg(array $options = []) Find image average.
 * @method float min(array $options = []) Find image minimum.
 * @method float max(array $options = []) Find image maximum.
 * @method float deviate(array $options = []) Find image standard deviation.
 * @method Image stats(array $options = []) Find image average.
 * @method Image hist_find(array $options = []) Find image histogram.
 * @method Image hist_find_ndim(array $options = []) Find n-dimensional image histogram.
 * @method Image hist_find_indexed(Image $index, array $options = []) Find indexed image histogram.
 * @method Image hough_line(array $options = []) Find hough line transform.
 * @method Image hough_circle(array $options = []) Find hough circle transform.
 * @method array project(array $options = []) Find image projections.
 *     Return array with: [
 *         'columns' => @type Image Sums of columns.
 *         'rows' => @type Image Sums of rows.
 *     ];
 * @method array profile(array $options = []) Find image profiles.
 *     Return array with: [
 *         'columns' => @type Image First non-zero pixel in column.
 *         'rows' => @type Image First non-zero pixel in row.
 *     ];
 * @method Image measure(integer $h, integer $v, array $options = []) Measure a set of patches on a colour chart.
 * @method float[]|float getpoint(integer $x, integer $y, array $options = []) Read a point from an image.
 * @method Image copy(array $options = []) Copy an image.
 * @method Image tilecache(array $options = []) Cache an image as a set of tiles.
 * @method Image linecache(array $options = []) Cache an image as a set of lines.
 * @method Image sequential(array $options = []) Check sequential access.
 * @method Image cache(array $options = []) Cache an image.
 * @method Image embed(integer $x, integer $y, integer $width, integer $height, array $options = []) Embed an image in a larger image.
 * @method Image flip(string $direction, array $options = []) Flip an image.
 *     @see Direction for possible values for $direction
 * @method Image insert(Image $sub, integer $x, integer $y, array $options = []) Insert image @sub into @main at @x, @y.
 * @method Image join(Image $in2, string $direction, array $options = []) Join a pair of images.
 *     @see Direction for possible values for $direction
 * @method static Image arrayjoin(Image[] $in, array $options = []) Join an array of images.
 * @method Image smartcrop(integer $width, integer $height, array $options = []) Extract an area from an image.
 * @method Image extract_band(integer $band, array $options = []) Extract band from an image.
 * @method Image bandjoin_const(float[]|float $c, array $options = []) Append a constant band to an image.
 * @method Image bandmean(array $options = []) Band-wise average.
 * @method Image bandbool(string $boolean, array $options = []) Boolean operation across image bands.
 *     @see OperationBoolean for possible values for $boolean
 * @method Image replicate(integer $across, integer $down, array $options = []) Replicate an image.
 * @method Image cast(string $format, array $options = []) Cast an image.
 *     @see BandFormat for possible values for $format
 * @method Image rot(string $angle, array $options = []) Rotate an image.
 *     @see Angle for possible values for $angle
 * @method Image rot45(array $options = []) Rotate an image.
 * @method Image autorot(array $options = []) Autorotate image by exif tag.
 * @method Image recomb(Image $m, array $options = []) Linear recombination with matrix.
 * @method Image bandfold(array $options = []) Fold up x axis into bands.
 * @method Image bandunfold(array $options = []) Unfold image bands into x axis.
 * @method Image flatten(array $options = []) Flatten alpha out of an image.
 * @method Image premultiply(array $options = []) Premultiply image alpha.
 * @method Image unpremultiply(array $options = []) Unpremultiply image alpha.
 * @method Image grid(integer $tile_height, integer $across, integer $down, array $options = []) Grid an image.
 * @method Image scale(array $options = []) Scale an image to uchar.
 * @method Image wrap(array $options = []) Wrap image origin.
 * @method Image zoom(integer $xfac, integer $yfac, array $options = []) Zoom an image.
 * @method Image subsample(integer $xfac, integer $yfac, array $options = []) Subsample an image.
 * @method Image msb(array $options = []) Pick most-significant byte from an image.
 * @method Image byteswap(array $options = []) Byteswap an image.
 * @method Image falsecolour(array $options = []) False-colour an image.
 * @method Image gamma(array $options = []) Gamma an image.
 * @method static Image black(integer $width, integer $height, array $options = []) Make a black image.
 * @method static Image gaussnoise(integer $width, integer $height, array $options = []) Make a gaussnoise image.
 * @method static Image text(string $text, array $options = []) Make a text image.
 * @method static Image xyz(integer $width, integer $height, array $options = []) Make an image where pixel values are coordinates.
 * @method static Image gaussmat(float $sigma, float $min_ampl, array $options = []) Make a gaussian image.
 * @method static Image logmat(float $sigma, float $min_ampl, array $options = []) Make a laplacian of gaussian image.
 * @method static Image eye(integer $width, integer $height, array $options = []) Make an image showing the eye's spatial response.
 * @method static Image grey(integer $width, integer $height, array $options = []) Make a grey ramp image.
 * @method static Image zone(integer $width, integer $height, array $options = []) Make a zone plate.
 * @method static Image sines(integer $width, integer $height, array $options = []) Make a 2d sine wave.
 * @method static Image mask_ideal(integer $width, integer $height, float $frequency_cutoff, array $options = []) Make an ideal filter.
 * @method static Image mask_ideal_ring(integer $width, integer $height, float $frequency_cutoff, float $ringwidth, array $options = []) Make an ideal ring filter.
 * @method static Image mask_ideal_band(integer $width, integer $height, float $frequency_cutoff_x, float $frequency_cutoff_y, float $radius, array $options = []) Make an ideal band filter.
 * @method static Image mask_butterworth(integer $width, integer $height, float $order, float $frequency_cutoff, float $amplitude_cutoff, array $options = []) Make a butterworth filter.
 * @method static Image mask_butterworth_ring(integer $width, integer $height, float $order, float $frequency_cutoff, float $amplitude_cutoff, float $ringwidth, array $options = []) Make a butterworth ring filter.
 * @method static Image mask_butterworth_band(integer $width, integer $height, float $order, float $frequency_cutoff_x, float $frequency_cutoff_y, float $radius, float $amplitude_cutoff, array $options = []) Make a butterworth_band filter.
 * @method static Image mask_gaussian(integer $width, integer $height, float $frequency_cutoff, float $amplitude_cutoff, array $options = []) Make a gaussian filter.
 * @method static Image mask_gaussian_ring(integer $width, integer $height, float $frequency_cutoff, float $amplitude_cutoff, float $ringwidth, array $options = []) Make a gaussian ring filter.
 * @method static Image mask_gaussian_band(integer $width, integer $height, float $frequency_cutoff_x, float $frequency_cutoff_y, float $radius, float $amplitude_cutoff, array $options = []) Make a gaussian filter.
 * @method static Image mask_fractal(integer $width, integer $height, float $fractal_dimension, array $options = []) Make fractal filter.
 * @method Image buildlut(array $options = []) Build a look-up table.
 * @method Image invertlut(array $options = []) Build an inverted look-up table.
 * @method static Image tonelut(array $options = []) Build a look-up table.
 * @method static Image identity(array $options = []) Make a 1d image where pixel values are indexes.
 * @method static Image fractsurf(integer $width, integer $height, float $fractal_dimension, array $options = []) Make a fractal surface.
 * @method static Image worley(integer $width, integer $height, array $options = []) Make a worley noise image.
 * @method static Image perlin(integer $width, integer $height, array $options = []) Make a perlin noise image.
 * @method static Image csvload(string $filename, array $options = []) Load csv from file.
 * @method static Image matrixload(string $filename, array $options = []) Load matrix from file.
 * @method static Image rawload(string $filename, integer $width, integer $height, integer $bands, array $options = []) Load raw data from a file.
 * @method static Image vipsload(string $filename, array $options = []) Load vips from file.
 * @method static Image analyzeload(string $filename, array $options = []) Load an analyze6 image.
 * @method static Image ppmload(string $filename, array $options = []) Load ppm from file.
 * @method static Image radload(string $filename, array $options = []) Load a radiance image from a file.
 * @method static Image pdfload(string $filename, array $options = []) Load pdf with libpoppler.
 * @method static Image pdfload_buffer(string $buffer, array $options = []) Load pdf with libpoppler.
 * @method static Image svgload(string $filename, array $options = []) Load svg with rsvg.
 * @method static Image svgload_buffer(string $buffer, array $options = []) Load svg with rsvg.
 * @method static Image gifload(string $filename, array $options = []) Load gif with giflib.
 * @method static Image gifload_buffer(string $buffer, array $options = []) Load gif with giflib.
 * @method static Image pngload(string $filename, array $options = []) Load png from file.
 * @method static Image pngload_buffer(string $buffer, array $options = []) Load png from buffer.
 * @method static Image matload(string $filename, array $options = []) Load mat from file.
 * @method static Image jpegload(string $filename, array $options = []) Load jpeg from file.
 * @method static Image jpegload_buffer(string $buffer, array $options = []) Load jpeg from buffer.
 * @method static Image webpload(string $filename, array $options = []) Load webp from file.
 * @method static Image webpload_buffer(string $buffer, array $options = []) Load webp from buffer.
 * @method static Image tiffload(string $filename, array $options = []) Load tiff from file.
 * @method static Image tiffload_buffer(string $buffer, array $options = []) Load tiff from buffer.
 * @method static Image openslideload(string $filename, array $options = []) Load file with openslide.
 * @method static Image magickload(string $filename, array $options = []) Load file with imagemagick.
 * @method static Image magickload_buffer(string $buffer, array $options = []) Load buffer with imagemagick.
 * @method static Image fitsload(string $filename, array $options = []) Load a fits image.
 * @method static Image openexrload(string $filename, array $options = []) Load an openexr image.
 * @method void csvsave(string $filename, array $options = []) Save image to csv file.
 * @method void matrixsave(string $filename, array $options = []) Save image to matrix file.
 * @method void matrixprint(array $options = []) Print matrix.
 * @method void rawsave(string $filename, array $options = []) Save image to raw file.
 * @method void rawsave_fd(integer $fd, array $options = []) Write raw image to file descriptor.
 * @method void vipssave(string $filename, array $options = []) Save image to vips file.
 * @method void ppmsave(string $filename, array $options = []) Save image to ppm file.
 * @method void radsave(string $filename, array $options = []) Save image to radiance file.
 * @method string radsave_buffer(array $options = []) Save image to radiance buffer.
 * @method void dzsave(string $filename, array $options = []) Save image to deepzoom file.
 * @method string dzsave_buffer(array $options = []) Save image to dz buffer.
 * @method void pngsave(string $filename, array $options = []) Save image to png file.
 * @method string pngsave_buffer(array $options = []) Save image to png buffer.
 * @method void jpegsave(string $filename, array $options = []) Save image to jpeg file.
 * @method string jpegsave_buffer(array $options = []) Save image to jpeg buffer.
 * @method void jpegsave_mime(array $options = []) Save image to jpeg mime.
 * @method void webpsave(string $filename, array $options = []) Save image to webp file.
 * @method string webpsave_buffer(array $options = []) Save image to webp buffer.
 * @method void tiffsave(string $filename, array $options = []) Save image to tiff file.
 * @method string tiffsave_buffer(array $options = []) Save image to tiff buffer.
 * @method void fitssave(string $filename, array $options = []) Save image to fits file.
 * @method static Image thumbnail(string $filename, integer $width, array $options = []) Generate thumbnail from file.
 * @method static Image thumbnail_buffer(string $buffer, integer $width, array $options = []) Generate thumbnail from buffer.
 * @method Image mapim(Image $index, array $options = []) Resample with an mapim image.
 * @method Image shrink(float $hshrink, float $vshrink, array $options = []) Shrink an image.
 * @method Image shrinkh(integer $hshrink, array $options = []) Shrink an image horizontally.
 * @method Image shrinkv(integer $vshrink, array $options = []) Shrink an image vertically.
 * @method Image reduceh(float $hshrink, array $options = []) Shrink an image horizontally.
 * @method Image reducev(float $vshrink, array $options = []) Shrink an image vertically.
 * @method Image reduce(float $hshrink, float $vshrink, array $options = []) Reduce an image.
 * @method Image quadratic(Image $coeff, array $options = []) Resample an image with a quadratic transform.
 * @method Image affine(float[]|float $matrix, array $options = []) Affine transform of an image.
 * @method Image similarity(array $options = []) Similarity transform of an image.
 * @method Image resize(float $scale, array $options = []) Resize an image.
 * @method Image colourspace(string $space, array $options = []) Convert to a new colourspace.
 *     @see Interpretation for possible values for $space
 * @method Image Lab2XYZ(array $options = []) Transform cielab to xyz.
 * @method Image XYZ2Lab(array $options = []) Transform xyz to lab.
 * @method Image Lab2LCh(array $options = []) Transform lab to lch.
 * @method Image LCh2Lab(array $options = []) Transform lch to lab.
 * @method Image LCh2CMC(array $options = []) Transform lch to cmc.
 * @method Image CMC2LCh(array $options = []) Transform lch to cmc.
 * @method Image XYZ2Yxy(array $options = []) Transform xyz to yxy.
 * @method Image Yxy2XYZ(array $options = []) Transform yxy to xyz.
 * @method Image scRGB2XYZ(array $options = []) Transform scrgb to xyz.
 * @method Image XYZ2scRGB(array $options = []) Transform xyz to scrgb.
 * @method Image LabQ2Lab(array $options = []) Unpack a labq image to float lab.
 * @method Image Lab2LabQ(array $options = []) Transform float lab to labq coding.
 * @method Image LabQ2LabS(array $options = []) Unpack a labq image to short lab.
 * @method Image LabS2LabQ(array $options = []) Transform short lab to labq coding.
 * @method Image LabS2Lab(array $options = []) Transform signed short lab to float.
 * @method Image Lab2LabS(array $options = []) Transform float lab to signed short.
 * @method Image rad2float(array $options = []) Unpack radiance coding to float rgb.
 * @method Image float2rad(array $options = []) Transform float rgb to radiance coding.
 * @method Image LabQ2sRGB(array $options = []) Convert a labq image to srgb.
 * @method Image sRGB2HSV(array $options = []) Transform srgb to hsv.
 * @method Image HSV2sRGB(array $options = []) Transform hsv to srgb.
 * @method Image icc_import(array $options = []) Import from device with icc profile.
 * @method Image icc_export(array $options = []) Output to device with icc profile.
 * @method Image icc_transform(string $output_profile, array $options = []) Transform between devices with icc profiles.
 * @method Image dE76(Image $right, array $options = []) Calculate de76.
 * @method Image dE00(Image $right, array $options = []) Calculate de00.
 * @method Image dECMC(Image $right, array $options = []) Calculate decmc.
 * @method Image sRGB2scRGB(array $options = []) Convert an srgb image to scrgb.
 * @method Image scRGB2BW(array $options = []) Convert scrgb to bw.
 * @method Image scRGB2sRGB(array $options = []) Convert an scrgb image to srgb.
 * @method Image maplut(Image $lut, array $options = []) Map an image though a lut.
 * @method integer percent(float $percent, array $options = []) Find threshold for percent of pixels.
 * @method Image stdif(integer $width, integer $height, array $options = []) Statistical difference.
 * @method Image hist_cum(array $options = []) Form cumulative histogram.
 * @method Image hist_match(Image $ref, array $options = []) Match two histograms.
 * @method Image hist_norm(array $options = []) Normalise histogram.
 * @method Image hist_equal(array $options = []) Histogram equalisation.
 * @method Image hist_plot(array $options = []) Plot histogram.
 * @method Image hist_local(integer $width, integer $height, array $options = []) Local histogram equalisation.
 * @method bool hist_ismonotonic(array $options = []) Test for monotonicity.
 * @method float hist_entropy(array $options = []) Estimate image entropy.
 * @method Image conv(Image $mask, array $options = []) Convolution operation.
 * @method Image conva(Image $mask, array $options = []) Approximate integer convolution.
 * @method Image convf(Image $mask, array $options = []) Float convolution operation.
 * @method Image convi(Image $mask, array $options = []) Int convolution operation.
 * @method Image compass(Image $mask, array $options = []) Convolve with rotating mask.
 * @method Image convsep(Image $mask, array $options = []) Seperable convolution operation.
 * @method Image convasep(Image $mask, array $options = []) Approximate separable integer convolution.
 * @method Image fastcor(Image $ref, array $options = []) Fast correlation.
 * @method Image spcor(Image $ref, array $options = []) Spatial correlation.
 * @method Image sharpen(array $options = []) Unsharp masking for print.
 * @method Image gaussblur(float $sigma, array $options = []) Gaussian blur.
 * @method Image fwfft(array $options = []) Forward fft.
 * @method Image invfft(array $options = []) Inverse fft.
 * @method Image freqmult(Image $mask, array $options = []) Frequency-domain filtering.
 * @method Image spectrum(array $options = []) Make displayable power spectrum.
 * @method Image phasecor(Image $in2, array $options = []) Calculate phase correlation.
 * @method Image morph(Image $mask, string $morph, array $options = []) Morphology operation.
 *     @see OperationMorphology for possible values for $morph
 * @method Image rank(integer $width, integer $height, integer $index, array $options = []) Rank filter.
 * @method float countlines(string $direction, array $options = []) Count lines in an image.
 *     @see Direction for possible values for $direction
 * @method Image labelregions(array $options = []) Label regions in an image.
 * @method Image draw_rect(float[]|float $ink, integer $left, integer $top, integer $width, integer $height, array $options = []) Paint a rectangle on an image.
 * @method Image draw_mask(float[]|float $ink, Image $mask, integer $x, integer $y, array $options = []) Draw a mask on an image.
 * @method Image draw_line(float[]|float $ink, integer $x1, integer $y1, integer $x2, integer $y2, array $options = []) Draw a line on an image.
 * @method Image draw_circle(float[]|float $ink, integer $cx, integer $cy, integer $radius, array $options = []) Draw a circle on an image.
 * @method Image draw_flood(float[]|float $ink, integer $x, integer $y, array $options = []) Flood-fill an area.
 * @method Image draw_image(Image $sub, integer $x, integer $y, array $options = []) Paint an image into another image.
 * @method Image draw_smudge(integer $left, integer $top, integer $width, integer $height, array $options = []) Blur a rectangle on an image.
 * @method Image merge(Image $sec, string $direction, integer $dx, integer $dy, array $options = []) Merge two images.
 *     @see Direction for possible values for $direction
 * @method Image mosaic(Image $sec, string $direction, integer $xref, integer $yref, integer $xsec, integer $ysec, array $options = []) Mosaic two images.
 *     @see Direction for possible values for $direction
 * @method Image mosaic1(Image $sec, string $direction, integer $xr1, integer $yr1, integer $xs1, integer $ys1, integer $xr2, integer $yr2, integer $xs2, integer $ys2, array $options = []) First-order mosaic of two images.
 *     @see Direction for possible values for $direction
 * @method Image match(Image $sec, integer $xr1, integer $yr1, integer $xs1, integer $ys1, integer $xr2, integer $yr2, integer $xs2, integer $ys2, array $options = []) First-order match of two images.
 * @method Image globalbalance(array $options = []) Global balance an image mosaic.
 * @method Image extract_area(integer $left, integer $top, integer $width, integer $height, array $options = []) Extract an area from an image.
 * @method Image crop(integer $left, integer $top, integer $width, integer $height, array $options = []) Extract an area from an image.
 *
 * @property string $nickname Class nickname
 * @property string $description Class description
 * @property integer $width Image width in pixels
 * @property integer $height Image height in pixels
 * @property integer $bands Number of bands in image
 * @property string $format Pixel format in image
 *     @see BandFormat for possible values
 * @property string $coding Pixel coding
 *     @see Coding for possible values
 * @property string $interpretation Pixel interpretation
 *     @see Interpretation for possible values
 * @property float $xres Horizontal resolution in pixels/mm
 * @property float $yres Vertical resolution in pixels/mm
 * @property integer $xoffset Horizontal offset of origin
 * @property integer $yoffset Vertical offset of origin
 * @property string $filename Image filename
 * @property string $mode Open mode
 * @property bool $kill Block evaluation on this image
 * @property string $demand Preferred demand style for this image
 *     @see DemandStyle for possible values
 * @property integer $sizeof_header Offset in bytes from start of file
 * @property string $foreign_buffer Pointer to foreign pixels
 */
abstract class ImageAutodoc
{
}
