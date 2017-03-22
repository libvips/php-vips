#!/bin/sh 

set -e
if [ ! -d "$HOME/vips/lib" ]; then
	wget $VIPS_SITE/$VIPS_VERSION/vips-$VIPS_VERSION_FULL.tar.gz
	tar xf vips-$VIPS_VERSION_FULL.tar.gz
	cd vips-$VIPS_VERSION_FULL
	CXXFLAGS=-D_GLIBCXX_USE_CXX11_ABI=0 ./configure --prefix=$HOME/vips \
	    --disable-debug \
	    --disable-dependency-tracking \
	    --disable-introspection \
	    --disable-static \
	    --enable-gtk-doc-html=no \
	    --enable-gtk-doc=no \
	    --enable-pyvips8=no \
	    --without-orc \
	    --without-python
	make && make install
else
	echo 'Using cached directory.'
fi
