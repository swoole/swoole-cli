<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $liblzma_prefix = LIBLZMA_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('liblzma'))
            ->withHomePage('https://tukaani.org/xz/')
            ->withLicense('https://github.com/tukaani-project/xz/blob/master/COPYING.GPLv3', Library::LICENSE_LGPL)
            ->withUrl('https://github.com/tukaani-project/xz/releases/download/v5.4.1/xz-5.4.1.tar.gz')
            ->withFile('xz-5.4.1.tar.gz')
            ->withPrefix($liblzma_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help
                ./configure \
                --prefix={$liblzma_prefix} \
                --enable-static=yes  \
                --enable-shared=no \
                --with-libiconv-prefix={$libiconv_prefix} \
                --without-libintl-prefix \
                --disable-doc
EOF
            )
            ->withPkgName('liblzma')
            ->withBinPath($liblzma_prefix . '/bin/')
            ->withDependentLibraries('libiconv')
    );
};
