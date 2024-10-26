<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libyuv_prefix = LIBYUV_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $lib = new Library('libyuv');
    $lib->withHomePage('https://chromium.googlesource.com/libyuv/libyuv')
        ->withLicense('https://chromium.googlesource.com/libyuv/libyuv/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://chromium.googlesource.com/libyuv/libyuv')
        ->withUrl('https://chromium.googlesource.com/libyuv/libyuv/+archive/refs/heads/stable.tar.gz')
        ->withFile('libyuv-stable.tar.gz')
        ->withPrefix($libyuv_prefix)
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

         cmake -S .. -B . \
        -DCMAKE_INSTALL_PREFIX={$libyuv_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DCMAKE_PREFIX_PATH="{$libjpeg_prefix}" \

        cmake --build . --config Release

        cmake --build . --config Release --target install


EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$libyuv_prefix}/lib/*.so.*
            rm -rf {$libyuv_prefix}/lib/*.so
            rm -rf {$libyuv_prefix}/lib/*.dylib
EOF
        )
        ->withBinPath($libyuv_prefix . '/bin/')
        ->withDependentLibraries('libjpeg')

    ;
    $p->addLibrary($lib);

};
