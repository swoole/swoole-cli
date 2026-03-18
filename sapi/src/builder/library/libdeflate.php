<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libdeflate_prefix = LIBDEFLATE_PREFIX;
    $lib = new Library('libdeflate');
    $lib->withHomePage('https://github.com/ebiggers/libdeflate')
        ->withLicense('https://github.com/ebiggers/libdeflate#MIT-1-ov-file', Library::LICENSE_MIT)
        ->withManual('https://github.com/ebiggers/libdeflate')
        ->withUrl('https://github.com/ebiggers/libdeflate/releases/download/v1.25/libdeflate-1.25.tar.gz')
        ->withPrefix($libdeflate_prefix)
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
        mkdir -p build_dir
        cd build_dir
        cmake -S .. -B . \
        -DCMAKE_INSTALL_PREFIX={$libdeflate_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DLIBDEFLATE_BUILD_SHARED_LIB=OFF  \
        -DLIBDEFLATE_BUILD_STATIC_LIB=ON \
        -DLIBDEFLATE_BUILD_TESTS=OFF

        cmake --build . --config Release

        cmake --build . --config Release --target install
EOF
        )
        ->withBinPath($libdeflate_prefix . '/bin/')
        ->withPkgName('libdeflate');

    $p->addLibrary($lib);
};


