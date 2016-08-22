# Experimental php binding for libvips 

This is an experimental php binding for libvips, just to see what it's like.

It currently only adds `vips_image_new_from_file()`, 
`vips_image_write_to_file()`, and the single operation `vips_invert()`, but it 
does seem to work. 

```php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);
	$x = vips_image_new_from_file($argv[1]);
	$x = vips_invert($x);
	vips_image_write_to_file($x, $argv[2]);
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

```
$ php vips.php 
Functions available in the test extension:
confirm_vips_compiled
vips_image_new_from_file
vips_image_write_to_file

Congratulations! You have successfully modified ext/vips/config.m4. Module vips
is now compiled into PHP.
```

Or this example:

```php
#!/usr/bin/env php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);
	$x = vips_image_new_from_file($argv[1]);
	$x = vips_invert($x);
	vips_image_write_to_file($x, $argv[2]);
?>
```

And run with:

```
$ ./try1.php ~/pics/k2.jpg x.tif
```

See `examples/`.

### TODO

* add `vips_call` to call any vips operation

  still need to do optional output args, more boxed types

* make a wrapper over this thing in php which gives it a nice API, including
  exceptions, overloads, and so on

### links

http://php.net/manual/en/internals2.php

https://devzone.zend.com/303/extension-writing-part-i-introduction-to-php-and-zend/

https://devzone.zend.com/317/extension-writing-part-ii-parameters-arrays-and-zvals/

https://devzone.zend.com/446/extension-writing-part-iii-resources/

