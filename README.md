# PHP binding for libvips 

This package lets you use the libvips image processing library from PHP. 

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

`vips.c` defines a simple but ugly way to call any libvips operation from PHP.
It uses libvips' own introspection facilities and does not depend on anything
else (so no gobject-introspection, for example). It's a fairly short 1,600
lines of C.

`vips.php` is a PHP layer over the ugly `vips.c` API that tries to make a nice
interface for programmers. It uses `__call()` and `__get()` to make all
libvips operations appear as methods, and all libvips properties as 
properties of the PHP `Vips\Image` class.  

### Preparation

PHP is normally built for speed and is missing a lot of debugging support you
need for extension development. For testing and dev, build your own php. 
I used 7.0.10 and configured with:

```
$ ./configure --prefix=/home/john/vips --enable-debug --enable-maintainer-zts \
	--enable-cgi --enable-cli --with-readline --with-openssl
```

### Regenerate build system

Run:

```
$ phpize
```

To scan `config.m4` and your php install and regenerate the build system.

### Configuring

Run

```
$ ./configure 
```

Check the output carefully for errors, and obviously check that it found your
libvips.

### Installing

Run:


```
$ make
```

To build the module to the `modules/` directory in this repository. 

Don't post php-vips test results to php.net! Stop this with:


```
$ export NO_INTERACTION=1
```


Test with:


```
$ make test
```

Finally, install to your php extensions area with:

```
$ make install
```

### Using

Try:

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

### Links

http://php.net/manual/en/internals2.php

https://devzone.zend.com/303/extension-writing-part-i-introduction-to-php-and-zend/

https://devzone.zend.com/317/extension-writing-part-ii-parameters-arrays-and-zvals/

https://devzone.zend.com/446/extension-writing-part-iii-resources/

### Documentation

```
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
