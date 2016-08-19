# Experimental php binding for libvips 

This is an experimental php binding for libvips, just to see what it's like.

It currently finds libvips, sets up the build system, builds and links with 
the correct set of cflags, and installs a minimal module. It does not actually
call any vips functions.

### Preparation

PHP is normally built for speed and is missing a lot of debugging support you
need for extension development. For testing and dev, build your own php. 
I used 7.0.10 and configured with:

```
$ ./configure --prefix=/home/john/vips --enable-debug --enable-maintainer-zts \
	--enable-cgi --enable-cli
```

### Regenerate build system

Run:

```
$ phpize
...
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

To build the module to the `modules/` directory in this repository. Test with:

```
$ make test
```

Finally, install to your php extensions area with:

```
$ make install
```
