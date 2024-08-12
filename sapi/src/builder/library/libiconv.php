<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libiconv'))
            ->withHomePage('https://www.gnu.org/software/libiconv/')
            ->withManual('https://www.gnu.org/software/libiconv/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            //->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.17.tar.gz')
            ->withUrl('https://ftpmirror.gnu.org/gnu/libiconv/libiconv-1.17.tar.gz')
            ->withFileHash('md5', 'd718cd5a59438be666d1575855be72c3')
            ->withPrefix($libiconv_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help

            ./configure \
            --prefix={$libiconv_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-extra-encodings
EOF
            )
            ->withBinPath($libiconv_prefix . '/bin/')
    );
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . ICONV_PREFIX . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . ICONV_PREFIX . '/lib');
    $p->withVariable('LIBS', '$LIBS -liconv');
};
