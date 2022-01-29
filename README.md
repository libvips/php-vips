# PHP binding for libvips 

[![Build Status](https://travis-ci.org/libvips/php-vips.svg?branch=master)](https://travis-ci.org/libvips/php-vips)

`php-vips` is a binding for [libvips](https://github.com/libvips/libvips) for
PHP 7.4 and later. 

libvips is fast and needs little memory. The
[`vips-php-bench`](https://github.com/jcupitt/php-vips-bench) repository
tests `php-vips` against `imagick` and `gd`. On that test, and on my laptop,
`php-vips` is around four times faster than `imagick` and needs 10 times
less memory.

Programs that use libvips don't manipulate images directly, instead they
create pipelines of image processing operations starting from a source
image. When the pipe is connected to a destination, the whole pipeline
executes at once and in parallel, streaming the image from source to
destination in a set of small fragments.

### Install

You need to [install the libvips
library](https://libvips.github.io/libvips/install.html). It's in the linux
package managers, homebrew and MacPorts, and there are Windows binaries on
the vips website. For example, on Debian:

```
sudo apt-get install libvips-dev
```

Or macOS:

```
brew install vips
```

Then add vips to your `composer.json`:

```
{
    "repositories": [
        {
            "type": "path",
            "url": "/your/local/path/to/php-vips"
        }
    ],
    "require": {
        "jcupitt/vips": "*"
    }
}
```

Once this is finished, switch to:

```
"require": {
    "jcupitt/vips" : "2.0.0"
}
```

### Example

```php
#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
use Jcupitt\Vips;

// fast thumbnail generator
$image = Vips\Image::thumbnail('somefile.jpg', 128);
$image->writeToFile('tiny.jpg');

// load an image, get fields, process, save
$image = Vips\Image::newFromFile($argv[1]);
echo "width = $image->width\n";
$image = $image->invert();
$image->writeToFile($argv[2]);
```

Run with:

```
$ composer install
$ ./try1.php ~/pics/k2.jpg x.tif
```

See `examples/`. We have a [complete set of formatted API
docs](https://libvips.github.io/php-vips/docs/classes/Jcupitt-Vips-Image.html).

### Introduction to the API

Almost all methods return a new image as the result, so you can chain them.
For example:

```php
$new_image = $image->more(12)->ifthenelse(255, $image);
```

will make a mask of pixels greater than 12, then use the mask to set pixels to
either 255 or the original image.

Note that libvips operators always make new images, they don't modify existing
images, so after the line above, `$image` is unchanged.

You use long, double, array and image as parameters. For example:

```php
$image = $image->add(2);
```

to add two to every band element, or:

```php
$image = $image->add([1, 2, 3]);
```

to add 1 to the first band, 2 to the second and 3 to the third. Or:

```php
$image = $image->add($image2);
```

to add two images. Or: 

```php
$image = $image->add([[1, 2, 3], [4, 5, 6]]);
```

To make a 2 x 3 image from the array, then add that image to the original.

Almost all methods can take an extra final argument: an array of options.
For example:

```php
$image->writeToFile("fred.jpg", ["Q" => 90]);
```

`php-vips` comes [with full API docs](https://libvips.github.io/php-vips/docs/classes/Jcupitt.Vips.Image.html). To regenerate these from your sources, type:

```
$ vendor/bin/phpdoc
```

And look in `docs/`.

There are around 300 operations in the library, see the vips docs for an
introduction:

https://libvips.github.io/libvips/API/current

### Test and install

```
$ composer install
$ composer test
$ vendor/bin/phpdoc
```

### Regenerate auto docs

```
$ cd src
$ ../examples/generate_phpdoc.py
```

