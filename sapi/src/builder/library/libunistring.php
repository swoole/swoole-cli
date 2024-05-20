<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libiconv_prefix = ICONV_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;

    $p->addLibrary(
        (new Library('libunistring'))
            ->withHomePage('https://www.gnu.org/software/libunistring/')
            ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
            //->withUrl('https://ftp.gnu.org/gnu/libunistring/libunistring-1.1.tar.gz')
            ->withUrl('https://ftpmirror.gnu.org/gnu/libunistring/libunistring-1.1.tar.gz')
            ->withPrefix($libunistring_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help
                ./configure \
                --prefix={$libunistring_prefix} \
                --with-libiconv-prefix={$libiconv_prefix} \
                --enable-shared=no \
                --enable-static=yes
EOF
            )
    );


    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libunistring_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libunistring_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lunistring ');
};
