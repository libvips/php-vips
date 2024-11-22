# PHP binding for libvips 

[![CI](https://github.com/libvips/php-vips/workflows/CI/badge.svg)](https://github.com/libvips/php-vips/actions)

`php-vips` is a binding for [libvips](https://github.com/libvips/libvips) 8.7
and later that runs on PHP 7.4 and later.

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
library](https://www.libvips.org/install.html). It's in the linux
package managers, homebrew and MacPorts, and there are Windows binaries on
the vips website. For example, on Debian:

```
sudo apt-get install --no-install-recommends libvips42
```

(`--no-install-recommends` stops Debian installing a *lot* of extra packages)

Or macOS:

```
brew install vips
```

You'll need to [enable FFI in your
PHP](https://www.php.net/manual/en/ffi.configuration.php), then add vips
to your `composer.json`:

```
"require": {
    "jcupitt/vips" : "2.4.0"
}
```

php-vips does not yet support preloading, so you need to enable FFI globally.
This has some security implications, since anyone who can run php on your
server can use it to call any native library they have access to.

Of course if attackers are running their own PHP code on your webserver you
are probably already toast, unfortunately.

Finally, on php 8.3 and later you need to disable stack overflow
tests. php-vips executes FFI callbacks off the main thread and this confuses
those checks, at least in php 8.3.0.

Add:

```
zend.max_allowed_stack_size=-1
```

To your `php.ini`.

#### Troubleshooting

To make sure to have all correctly installed and your php correctly configure use the following script in your terminal:

```bash
php -r 'echo "Has FFI Extension: " . (extension_loaded("ffi") ? "true" : "false") . PHP_EOL;
    echo "Has FFI Class: " . (class_exists(FFI::class) ? "true" : "false") . PHP_EOL;
    echo "Has FFI Enabled: " . (ini_get("ffi.enable") === "1" ? "true" : "false") . PHP_EOL;
    echo "Has zend.max_allowed_stack_size correct: " . (ini_get("zend.max_allowed_stack_size") === "-1" ? "true" : "false") . PHP_EOL;
    try {
        require_once("vendor/autoload.php");
        $libVipsInstalled = \\Jcupitt\\Vips\\Config::version();
        echo "Has LibVips Installed: " . $libVipsInstalled . PHP_EOL;
    } catch (\Exception $e) {
        echo "Has LibVips Installed: false " . PHP_EOL;
        echo PHP_EOL . "Error: " . $libVipsError = $e->getMessage();
    }'
```

It should output:

```bash
Has FFI Extension: true
Has FFI Class: true
Has FFI Enabled: true
Has zend.max_allowed_stack_size correct: true
Has LibVips Installed: 8.16.0
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
docs](https://libvips.github.io/php-vips/classes/Jcupitt-Vips-Image.html).


### How it works

php-vips uses [php-ffi](https://www.php.net/manual/en/book.ffi.php) to
call directly into the libvips binary. It introspects the library binary
and presents the methods it finds as members of the `Image` class.

This means that the API you see depends on the version of libvips that
php-vips finds at runtime, and not on php-vips. php-vips documentation assumes
you are using the latest stable version of the libvips library.

The previous php-vips version that relied on a binary extension
and not on php-ffi is still available and supported in [the 1.x
branch](https://github.com/libvips/php-vips/tree/1.x).

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

You can use long, double, array and image as parameters. For example:

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

To make a 3 x 2 image from the array, then add that image to the original.

Almost all methods can take an extra final argument: an array of options.
For example:

```php
$image->writeToFile("fred.jpg", ["Q" => 90]);
```

`php-vips` comes [with API
docs](https://libvips.github.io/php-vips/classes/Jcupitt-Vips-Image.html).
To regenerate these from your sources, type:

```
$ vendor/bin/phpdoc
```

And look in `docs/`.

Unfortunatly, due to php-doc limitations, these do not list every option
to every operation. For a full API description you need to see the main
libvips documentation:

https://libvips.org/API/current

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
