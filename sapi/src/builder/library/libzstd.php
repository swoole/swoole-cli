<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libzstd_prefix = LIBZSTD_PREFIX;
    $liblz4_prefix = LIBLZ4_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $p->addLibrary(
        (new Library('libzstd'))
            ->withHomePage('https://github.com/facebook/zstd')
            ->withLicense('https://github.com/facebook/zstd/blob/dev/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://github.com/facebook/zstd/releases/download/v1.5.2/zstd-1.5.2.tar.gz')
            ->withFile('zstd-1.5.2.tar.gz')
            ->withPrefix($libzstd_prefix)
            ->withConfigure(
                <<<EOF
            mkdir -p build/cmake/builddir
            cd build/cmake/builddir
            # cmake -LH ..
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libzstd_prefix} \
            -DZSTD_BUILD_STATIC=ON \
            -DZSTD_BUILD_SHARED=OFF \
            -DCMAKE_BUILD_TYPE=Release \
            -DZSTD_BUILD_CONTRIB=ON \
            -DZSTD_BUILD_PROGRAMS=ON \
            -DZSTD_BUILD_TESTS=OFF \
            -DZSTD_LEGACY_SUPPORT=ON \
            -DZSTD_MULTITHREAD_SUPPORT=ON \
            -DZSTD_ZLIB_SUPPORT=ON \
            -DZSTD_LZMA_SUPPORT=ON \
            -DZSTD_LZ4_SUPPORT=ON \
            -DZLIB_ROOT={$zlib_prefix} \
            -DLibLZMA_ROOT={$liblzma_prefix} \
            -DLibLZ4_ROOT={$liblz4_prefix}
EOF
            )
            ->withMakeOptions('lib')
            ->withPkgName('libzstd')
            ->withBinPath($libzstd_prefix . '/bin')
            ->withDependentLibraries('liblz4', 'liblzma')
    );
};
