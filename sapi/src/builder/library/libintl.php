<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    //(gettext库包含libintl 、coreutils库包含libunistring

    $libintl_prefix = LIBINTL_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $ncurses_prefix = NCURSES_PREFIX;
    $p->addLibrary(
        (new Library('libintl'))
            ->withHomePage('https://www.gnu.org/software/gettext/')
            ->withLicense('https://www.gnu.org/licenses/licenses.html', Library::LICENSE_GPL)
            ->withManual('https://www.gnu.org/software/gettext/')
            //->withUrl('https://ftp.gnu.org/gnu/gettext/gettext-0.22.tar.xz')
            ->withUrl('https://ftpmirror.gnu.org/gnu/gettext/gettext-0.22.tar.xz')
            ->withPrefix($libintl_prefix)
            ->withConfigure(
                <<<EOF

            ./configure --help

            ./configure \
            --prefix={$libintl_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-relocatable \
            --enable-year2038 \
            --with-libiconv-prefix={$iconv_prefix} \
            --with-libncurses-prefix={$ncurses_prefix} \
            --with-libxml2-prefix={$libxml2_prefix} \
            --with-libunistring-prefix={$libunistring_prefix} \
            --without-emacs \
            --without-lispdir \
            --without-cvs \
            --disable-acl \
            --disable-java \
            --disable-csharp \
            --without-git


EOF
            )
            ->withDependentLibraries('libunistring', 'libiconv', 'ncurses', 'libxml2')
    );

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libintl_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libintl_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lintl ');
};
