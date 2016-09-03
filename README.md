# use libvips from PHP 

A high-level, fluent interface to the libvips image processing library. This
module depends upon the `vips-base` extension. 

The `vips-base` extension is very low-level. This module builds on that to make
a high-level, fluent interface, very similar to the libvips bindings for Ruby
and Python. 

`vips` is fast and it can work without needing to have the 
entire image loaded into memory. Programs that use `vips` don't
manipulate images directly, instead they create pipelines of image processing
operations building on a source image. When the end of the pipe is connected
to a destination, the whole pipline executes at once, streaming the image
in parallel from source to destination a section at a time. 

See also [benchmarks at the official libvips
website](http://www.vips.ecs.soton.ac.uk/index.php?title=Speed_and_Memory_Use).
There's a handy blog post explaining [how libvips opens
files](http://libvips.blogspot.co.uk/2012/06/how-libvips-opens-file.html)
which gives some more background.

### Example

```php
#!/usr/bin/env php
<?php
include 'vips.php';

$image = Vips\Image::newFromFile($argv[1]);

echo "width = ", $image->width, "\n";

$image = $image->invert();

$image->writeToFile($argv[2]);
?>
```

And run with:

```
$ ./try1.php ~/pics/k2.jpg x.tif
```

See `examples/`.

Almost all methods return a new image for the result, so you can chain them.
For example:

```
$image = $image->more(12)->ifthenelse(255, $image);
```

will make a mask of pixels greater than 12, then use the mask to set pixels to
either 255 or the original image.

You use long, double, array and image as parameters. For example:

```
$image = $image->add(2);
```

to add two to every band element, or:

```
$image = $image->add([1, 2, 3]);
```

to add 1 to the first band, 2 to the second and 3 to the third. Or:

```
$image = $image->add($image2);
```

to add two images. 

Almost all methods can take an optional final argument, an array of options.
For example:

```
$image->writeToFile("fred.jpg", ["Q" => 90]);
```

There are around 300 operations in the library, see the vips docs for an
introduction:

http://www.vips.ecs.soton.ac.uk/supported/current/doc/html/libvips/

### How it works

`vips-base` defines a simple but ugly way to call any libvips operation from PHP.
It uses libvips' own introspection facilities and does not depend on anything
else (so no gobject-introspection, for example). It's a fairly short 1,600
lines of C.

This module is a PHP layer over the ugly `vips-base` API that tries to make a 
nice interface for programmers. It uses `__call()` and `__get()` to make all
libvips operations appear as methods, and all libvips properties as 
properties of the PHP `Vips\Image` class.  

### Documentation

```
$ phpcs src
$ pear channel-discover pear.phpdoc.org
$ pear install phpdoc/phpDocumentor
$ phpdoc 
```

### imagick

```
$ pecl install imagick
$ php -a
Interactive mode enabled
php > dl('imagick.' . PHP_SHLIB_SUFFIX);
php > $im = new Imagick();
```

