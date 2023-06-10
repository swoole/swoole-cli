<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $liblzma_prefix = LIBLZMA_PREFIX;
    $p->addLibrary(
        (new Library('liblzma'))
            ->withHomePage('https://tukaani.org/xz/')
            ->withLicense('https://github.com/tukaani-project/xz/blob/master/COPYING.GPLv3', Library::LICENSE_LGPL)
            ->withUrl('https://github.com/tukaani-project/xz/releases/download/v5.4.1/xz-5.4.1.tar.gz')
            ->withFile('xz-5.4.1.tar.gz')
            ->withPrefix($liblzma_prefix)
            ->withConfigure(
                <<<EOF
                mkdir -p build
                cd build
                cmake .. \
                -DCMAKE_INSTALL_PREFIX={$liblzma_prefix} \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_SHARED_LIBS=OFF  \
                -DBUILD_STATIC_LIBS=ON
EOF
            )
            ->withPkgName('liblzma')
            ->withBinPath($liblzma_prefix . '/bin/')
    );
};
