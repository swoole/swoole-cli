<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libjpeg_prefix = JPEG_PREFIX;
    $lib = new Library('libjpeg');
    $lib->withHomePage('https://libjpeg-turbo.org/')
        ->withManual('https://libjpeg-turbo.org/Documentation/Documentation')
        ->withLicense('https://github.com/libjpeg-turbo/libjpeg-turbo/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
        ->withFile('libjpeg-turbo-2.1.2.tar.gz')
        ->withPrefix($libjpeg_prefix)
        ->withConfigure(
            <<<EOF
            cmake -G"Unix Makefiles"   . \
            -DCMAKE_INSTALL_PREFIX={$libjpeg_prefix} \
            -DCMAKE_INSTALL_LIBDIR={$libjpeg_prefix}/lib \
            -DCMAKE_INSTALL_INCLUDEDIR={$libjpeg_prefix}/include \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DENABLE_SHARED=OFF  \
            -DENABLE_STATIC=ON

EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$libjpeg_prefix}/lib/*.so.*
            rm -rf {$libjpeg_prefix}/lib/*.so
            rm -rf {$libjpeg_prefix}/lib/*.dylib
EOF
        )
        ->withPkgName('libjpeg')
        ->withPkgName('libturbojpeg')
        ->withBinPath($libjpeg_prefix . '/bin/');

    $p->addLibrary($lib);
};
