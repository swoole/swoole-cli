dnl config.m4 for the embedded SAPI (libphp.a)
dnl
dnl swoole-cli 移除了 php-src 原生 SAPI 选择机制（configure.ac 中 PHP_SAPI=none，
dnl 且 PHP_SELECT_SAPI 会覆盖 PHP_SAPI / OVERALL_TARGET / install_sapi），
dnl 因此这里不使用 PHP_SELECT_SAPI，只把 php_embed.c 编译进独立的 PHP_EMBED_OBJS。
dnl 这样 --enable-embed 对 bin/swoole-cli 的构建零影响，
dnl 仅额外提供一个 libs/libphp.a 目标。

PHP_ARG_ENABLE([embed],,
  [AS_HELP_STRING([--enable-embed],
    [Enable building of the embedded SAPI static library libs/libphp.a])],
  [no],
  [no])

AC_MSG_CHECKING([for embedded SAPI static library (libphp.a)])

if test "$PHP_EMBED" != "no"; then
  AC_MSG_RESULT([yes])

  PHP_ADD_BUILD_DIR([sapi/embed])
  PHP_ADD_SOURCES_X([sapi/embed],
    [php_embed.c],
    [-DZEND_ENABLE_STATIC_TSRMLS_CACHE=1],
    [PHP_EMBED_OBJS])

  PHP_SUBST([PHP_EMBED_OBJS])
  PHP_INSTALL_HEADERS([sapi/embed], [php_embed.h])
  PHP_ADD_MAKEFILE_FRAGMENT($abs_srcdir/sapi/embed/Makefile.frag)
else
  AC_MSG_RESULT([no])
fi
