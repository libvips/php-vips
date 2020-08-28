#!/bin/bash 

version=$VIPS_VERSION
vips_tarball=https://github.com/libvips/libvips/releases/download/v$version/vips-$version.tar.gz

set -e

# do we already have the correct vips built? early exit if yes
# we could check the configure params as well I guess
if [ -d "$HOME/vips/bin" ]; then
  installed_version=$($HOME/vips/bin/vips --version | awk -F- '{print $2}')
  echo "Need vips $version"
  echo "Found vips $installed_version"

  if [ "$installed_version" == "$version" ]; then
    echo "Using cached vips directory"
    exit 0
  fi
fi

rm -rf $HOME/vips
curl -Ls $vips_tarball | tar xz
cd vips-$version
CXXFLAGS=-D_GLIBCXX_USE_CXX11_ABI=0 ./configure --prefix=$HOME/vips "$@"
make -j`nproc` && make install
