<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openjpeg_prefix = OPENJPEG_PREFIX;

    $libtiff_prefix = LIBTIFF_PREFIX;
    $libpng_prefix = PNG_PREFIX;
    $libzlib_prefix = ZLIB_PREFIX;
    $lcms2_prefix = LCMS2_PREFIX;

    $cmake_prefix_path = "";
    $cmake_prefix_path .= "{$lcms2_prefix};";
    $cmake_prefix_path .= "{$libtiff_prefix};";
    $cmake_prefix_path .= "{$libpng_prefix};";
    $cmake_prefix_path .= "{$libzlib_prefix};";

    $lib = new Library('openjpeg');
    $lib->withHomePage('http://www.openjpeg.org/')
        ->withLicense('https://github.com/uclouvain/openjpeg?tab=License-1-ov-file#readme', Library::LICENSE_BSD)
        ->withManual('https://github.com/uclouvain/openjpeg/wiki/Installation')
        ->withUrl('https://github.com/uclouvain/openjpeg/archive/refs/tags/v2.5.4.tar.gz')
        ->withfile('openjpeg-v2.5.4.tar.gz')
        ->withPrefix($openjpeg_prefix)
        ->withConfigure(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$openjpeg_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DCMAKE_POLICY_VERSION_MINIMUM=3.5 \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_PREFIX_PATH="{$cmake_prefix_path}" \
            -DPNG_ROOT={$libpng_prefix} \
            -DTIFF_ROOT={$libtiff_prefix} \
            -DLCMS2_ROOT={$lcms2_prefix} \
            -DBUILD_CODEC=OFF

            cmake --build . --target install
EOF
        )
        ->withPkgName('libopenjp2')
        ->withDependentLibraries(
            'libpng',
            'libtiff',
            'lcms2',
            'zlib'
        );

    $p->addLibrary($lib);
};
