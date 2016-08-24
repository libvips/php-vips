# Experimental PHP binding for libvips 

This is an incomplete and experimental PHP binding for libvips.

### Examples

```php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	$image = vips_image_new_from_file($argv[1]);

	# you can call any vips operation 
	$result = vips_call("invert", $image);

	# the result is an array of the output objects, we just want the
	# image
	$image = $result["out"];

	vips_image_write_to_file($image, $argv[2]);
?>
```

```php
<?php
	dl('vips.' . PHP_SHLIB_SUFFIX);

	# you can pass optional arguments as a final array arg
	# enums can be set with a string
	$image = vips_image_new_from_file($argv[1], array("access" => "sequential"));

	# set args to true to get optional output args added to result
	$result = vips_call("min", $image, array("x" => true, "y" => true));

	$mn = $result["out"];
	$x = $result["x"];
	$y = $result["y"];

	# show position of minimum
	echo "min = ", $mn, "\n";
	echo "x = ", $x, "\n";
	echo "y = ", $y, "\n";
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
	dl('vips.' . PHP_SHLIB_SUFFIX);
	$x = vips_image_new_from_file($argv[1]);
	$x = vips_call("invert", $x)["out"];
	vips_image_write_to_file($x, $argv[2]);
?>
```

And run with:

```
$ ./try1.php ~/pics/k2.jpg x.tif
```

See `examples/`.

### TODO

* make a wrapper over this thing in php which gives it a nice API, including
  exceptions, automatic member lookup, properties, and so on

* use python to generate a lot of `vips.php`

### links

http://php.net/manual/en/internals2.php

https://devzone.zend.com/303/extension-writing-part-i-introduction-to-php-and-zend/

https://devzone.zend.com/317/extension-writing-part-ii-parameters-arrays-and-zvals/

https://devzone.zend.com/446/extension-writing-part-iii-resources/

