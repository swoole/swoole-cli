<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libiconv_prefix = ICONV_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    if (0) {
        $p->addLibrary(
            (new Library('libunistring'))
                ->withHomePage('https://www.gnu.org/software/libunistring/')
                ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
                ->withUrl('https://ftp.gnu.org/gnu/libunistring/libunistring-1.1.tar.gz')
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
    }
};
