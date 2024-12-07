<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libgav1_prefix = LIBGAV1_PREFIX;
    $p->addLibrary(
        (new Library('libgav1'))
            ->withHomePage('https://chromium.googlesource.com/codecs/libgav1')
            ->withLicense(
                'https://chromium.googlesource.com/codecs/libgav1/+/refs/heads/main/LICENSE',
                Library::LICENSE_APACHE2
            )
            ->withManual('https://chromium.googlesource.com/codecs/libgav1/+/refs/heads/main')
            ->withUrl('https://chromium.googlesource.com/codecs/libgav1/+archive/e386d8f1fb983200972d159b9be47fd5d0776708.tar.gz')
            ->withFile('libgav1-v0.19.0.tar.gz')
            ->withPrefix($libgav1_prefix)
            ->withUntarArchiveCommand('tar-default')
            ->withBuildCached(false)
            ->withConfigure(
                <<<EOF
            mkdir -p build
            cd build
            cmake -G "Unix Makefiles" .. \
            -DCMAKE_INSTALL_PREFIX={$libgav1_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DLIBGAV1_ENABLE_TESTS=OFF \
            -DLIBGAV1_ENABLE_EXAMPLES=OFF \
            -DLIBGAV1_THREADPOOL_USE_STD_MUTEX=1

EOF
            )
            ->withPkgName('libgav1')
            ->withBinPath($libgav1_prefix . '/bin/')
    );
};
