<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $blender_prefix = BLENDER_PREFIX;
    $lib = new Library('blender');
    $lib->withHomePage('https://developer.blender.org/')
        ->withLicense('https://www.blender.org/about/license/', Library::LICENSE_GPL)
        ->withManual('https://wiki.blender.org/wiki/Main_Page')
        ->withUrl('https://mirrors.aliyun.com/blender/source/blender-2.93.18.tar.xz')
        ->withHttpProxy(false) //不走代理
        ->withUntarArchiveCommand('xz')
        ->withPrefix($blender_prefix)
        ->withConfigure(
            <<<EOF
            mkdir -p build
            cd build
             cmake ..  \
            -DCMAKE_INSTALL_PREFIX={$blender_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DENABLE_DOCS=0 \
            -DENABLE_TESTS=0
EOF
        )
        ->withBinPath($blender_prefix . '/bin/')
        ->withPkgName('blender');

    $p->addLibrary($lib);
};
