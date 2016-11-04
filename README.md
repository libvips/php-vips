# High-level PHP binding for libvips 

`php-vips` is a binding for [libvips](https://github.com/jcupitt/libvips), in
line with [ruby-vips](https://github.com/jcupitt/ruby-vips),
[pyvips](https://github.com/jcupitt/libvips/tree/master/python), the [libvips
C++ API](https://github.com/jcupitt/libvips/tree/master/cplusplus) and the
[libvips C API](https://github.com/jcupitt/libvips/tree/master/libvips).  
It aims to be small and simple, and to provide a stable, well-documented
platform that conforms to [PHP standards
recommendations](http://www.php-fig.org/psr/).

libvips is fast and it can work without needing to have the 
entire image loaded into memory. Programs that use libvips don't
manipulate images directly, instead they create pipelines of image processing
operations starting from a source image. When the pipe is connected
to a destination, the whole pipeline executes at once, streaming the image
in parallel from source to destination in a set of small fragments. 

See the [benchmarks at the official libvips
website](http://www.vips.ecs.soton.ac.uk/index.php?title=Speed_and_Memory_Use).
There's also a [vips-php-bench](
https://github.com/jcupitt/php-vips-bench) repository with benchmarks against
imagick and gd. 
There's a handy blog post explaining [how libvips opens
files](http://libvips.blogspot.co.uk/2012/06/how-libvips-opens-file.html)
which gives some more background.

Extra functionality, such as an image thumbnailer, for example, is 
implemented in repositories which build on this one, and not incorporated
directly in this code (though see `examples/`).
As much of the binding as possible is auto-generated. This reduces
maintenance effort, increases reliability, and makes it possible to
reuse core libvips documentation.

This
module builds upon the `vips` binary extension, see:

https://github.com/jcupitt/php-vips-ext

You'll need to install that first. It's tested on Linux and OS X, 
Windows would need some work, but should be possible.  

### Example

```php
#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
use Jcupitt\Vips;

$image = Vips\Image::newFromFile($argv[1]);

echo "width = ", $image->width, "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);
?>
```

You'll need this in your `composer.json`:

```
    "require": {
            "jcupitt/vips" : "1.0.0"
    }
```

And run with:

```
$ composer install
$ ./try1.php ~/pics/k2.jpg x.tif
```

See `examples/`.

### Introduction to the API

Almost all methods return a new image for the result, so you can chain them.
For example:

```php
$image = $image->more(12)->ifthenelse(255, $image);
```

will make a mask of pixels greater than 12, then use the mask to set pixels to
either 255 or the original image.

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

`php-vips` comes with full API docs, run:

```
$ vendor/bin/phpdoc
```

And look in `docs/`.

There are around 300 operations in the library, see the vips docs for an
introduction:

http://www.vips.ecs.soton.ac.uk/supported/current/doc/html/libvips/

### How it works

The `vips` extension defines a simple but ugly way to call any libvips
operation from PHP.  It uses libvips' own introspection facilities
and does not depend on anything else (so no gobject-introspection,
for example). It's a fairly short 1,600 lines of C.

This module is a PHP layer over the ugly `vips` extension that
tries to make a nice interface for programmers. It uses `__call()` and
`__get()` to make all libvips operations appear as methods, and all
libvips properties as properties of the PHP `Vips\Image` class.

### Test and install

```
$ phpcs --standard=PSR2 src
$ php ~/packages/php/composer.phar install
$ vendor/bin/phpunit
$ vendor/bin/phpdoc
```

### Regenerate auto docs

```
$ cd src
$ ../examples/generate_phpdoc.rb
```

