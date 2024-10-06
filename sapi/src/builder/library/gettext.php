<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    // gettext 包含 libintl 库

    $gettext_prefix = GETTEXT_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $ncurses_prefix = NCURSES_PREFIX;
    $p->addLibrary(
        (new Library('gettext'))
            ->withHomePage('https://www.gnu.org/software/gettext/')
            ->withLicense('https://www.gnu.org/licenses/licenses.html', Library::LICENSE_GPL)
            ->withManual('https://www.gnu.org/software/gettext/')
            //->withUrl('https://ftp.gnu.org/gnu/gettext/gettext-0.22.5.tar.gz')
            ->withUrl('https://ftpmirror.gnu.org/gettext/gettext-0.22.5.tar.gz')
            ->withFileHash('sha256', "ec1705b1e969b83a9f073144ec806151db88127f5e40fe5a94cb6c8fa48996a0")
            ->withPrefix($gettext_prefix)
            //->withInstallCached(false)
            ->withConfigure(
                <<<EOF

            ./configure --help

            PACKAGES='zlib  '
            PACKAGES="\$PACKAGES libxml-2.0"
            PACKAGES="\$PACKAGES ncursesw"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) "
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) "
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) "

            CPPFLAGS="\${CPPFLAGS} -I{$libunistring_prefix}/include/ -I{$iconv_prefix}/include/"  \
            LDFLAGS="\${LDFLAGS} -L{$libunistring_prefix}/lib/ -L{$iconv_prefix}/lib/" \
            LIBS="\${LIBS} -liconv -lunistring " \
            ./configure \
            --prefix={$gettext_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-relocatable \
            --enable-year2038 \
            --with-pic \
            --with-libiconv-prefix={$iconv_prefix} \
            --with-libncurses-prefix={$ncurses_prefix} \
            --with-libxml2-prefix={$libxml2_prefix} \
            --with-libunistring-prefix={$libunistring_prefix} \
            --without-emacs \
            --without-lispdir \
            --disable-acl \
            --disable-java \
            --disable-csharp \
            --without-cvs \
            --without-git \
            --without-xz

EOF
            )
            ->withDependentLibraries('libunistring', 'libiconv', 'ncurses', 'libxml2', 'zlib')
    );

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $gettext_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $gettext_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lintl ');
};
