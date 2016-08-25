# Experimental PHP binding for libvips 

This is an experimental PHP binding for libvips. Not quite done yet, but it does more or less work. 

### Examples

```php
#!/usr/bin/env php
<?php
	include 'vips.php';

	$image = VImage::new_from_file($argv[1]); 

	echo "width = ", $image->width, "\n";

	$image = $image->invert();

	$image->write_to_file($argv[2]);
?>
```

### Preparation

PHP is normally built for speed and is missing a lot of debugging support you
need for extension development. For testing and dev, build your own php. 
I used 7.0.10 and configured with:

```
$ ./configure --prefix=/home/john/vips --enable-debug --enable-maintainer-zts \
	--enable-cgi --enable-cli --with-readline
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

	$image = VImage::new_from_file($argv[1]); 

	echo "width = ", $image->width, "\n";

	$image = $image->invert();

	$image->write_to_file($argv[2]);
?>
```

And run with:

```
$ ./try1.php ~/pics/k2.jpg x.tif
```

See `examples/`.

### TODO

* we need a `this` param to `vips_php_call`, meaning first input image

* `__call` needs to unwrap single element array returns

* `__call` needs to wrap image returns up as `VImage`

* `__call` needs to generate a member not found message

* add `__callStatic` class methods

* use python to generate a lot of `vips.php`

  we'll want phpDoc for the magic methods and properties, at least

### links

http://php.net/manual/en/internals2.php

https://devzone.zend.com/303/extension-writing-part-i-introduction-to-php-and-zend/

https://devzone.zend.com/317/extension-writing-part-ii-parameters-arrays-and-zvals/

https://devzone.zend.com/446/extension-writing-part-iii-resources/

