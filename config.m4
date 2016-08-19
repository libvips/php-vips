dnl $Id$
dnl config.m4 for extension vips

PHP_ARG_WITH(vips, for vips support,
[  --with-vips             Include vips support])

VIPS_MIN_VERSION=8.3

if test x"$PHP_VIPS" != x"no"; then
  if ! pkg-config --atleast-pkgconfig-version 0.2; then
    AC_MSG_ERROR([you need at least pkg-config 0.2 for this module])
    PHP_VIPS=no
  fi
fi

if test x"$PHP_VIPS" != x"no"; then
  if ! pkg-config vips --atleast-version $VIPS_MIN_VERSION; then
    AC_MSG_ERROR([you need at least vips $VIPS_MIN_VERSION for this module])
    PHP_VIPS=no
  fi
fi

if test x"$PHP_VIPS" != x"no"; then
  VIPS_CFLAGS=`pkg-config vips --cflags-only-other`
  VIPS_INCS=`pkg-config vips --cflags-only-I`
  VIPS_LIBS=`pkg-config vips --libs`

  PHP_CHECK_LIBRARY(vips, vips_init,
  [
    PHP_EVAL_INCLINE($VIPS_INCS)
    PHP_EVAL_LIBLINE($VIPS_LIBS, VIPS_SHARED_LIBADD)
  ],[
    AC_MSG_ERROR([libvips not found.  Check config.log for more information.])
  ],[$VIPS_LIBS]
  )

  AC_DEFINE(HAVE_VIPS, 1, [Whether you have vips])
  PHP_NEW_EXTENSION(vips, vips.c, $ext_shared,, -DZEND_ENABLE_STATIC_TSRMLS_CACHE=1 $VIPS_CFLAGS)
  PHP_SUBST(VIPS_SHARED_LIBADD)
fi

