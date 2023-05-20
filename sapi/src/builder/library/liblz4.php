<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;


return function (Preprocessor $p) {
    $liblz4_prefix = LIBLZ4_PREFIX;
    $p->addLibrary(
        (new Library('liblz4'))
            ->withHomePage('http://www.lz4.org')
            ->withManual('http://www.lz4.org')
            ->withLicense('https://github.com/lz4/lz4/blob/dev/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/lz4/lz4/archive/refs/tags/v1.9.4.tar.gz')
            ->withFile('lz4-v1.9.4.tar.gz')
            ->withPkgName('liblz4')
            ->withPrefix($liblz4_prefix)
            ->withConfigure(
                <<<EOF
            cd build/cmake/
            cmake . -DCMAKE_INSTALL_PREFIX={$liblz4_prefix}  -DBUILD_SHARED_LIBS=OFF  -DBUILD_STATIC_LIBS=ON
EOF
            )
            ->withBinPath($liblz4_prefix . '/bin')
            ->withPkgName('liblz4')
    );
};
