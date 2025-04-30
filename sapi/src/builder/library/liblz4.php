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
            ->withFileHash('md5', 'e9286adb64040071c5e23498bf753261')
            ->withPkgName('liblz4')
            ->withPrefix($liblz4_prefix)
            ->withConfigure(
                <<<EOF
                mkdir -p build/cmake/build
                cd build/cmake/build
                # cmake -LH ..
                cmake .. \
                -DCMAKE_INSTALL_PREFIX={$liblz4_prefix} \
                -DCMAKE_BUILD_TYPE=Release \
                -DBUILD_SHARED_LIBS=OFF \
                -DBUILD_STATIC_LIBS=ON \
                -DLZ4_POSITION_INDEPENDENT_LIB=ON \
                -DLZ4_BUILD_LEGACY_LZ4C=ON \
                -DLZ4_BUILD_CLI=ON \
                -DCMAKE_POLICY_VERSION_MINIMUM=3.5
EOF
            )
            ->withBinPath($liblz4_prefix . '/bin')
            ->withPkgName('liblz4')
    );
};
