<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libjxl_prefix = LIBJXL_PREFIX;
    $brotli_prefix = BROTLI_PREFIX;
    $libgif_prefix = GIF_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $libpng_prefix = PNG_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libwebp_prefix = WEBP_PREFIX;
    $cmake_prefix_path = "";
    $cmake_prefix_path .= "{$brotli_prefix};";
    $cmake_prefix_path .= "{$libgif_prefix};";
    $cmake_prefix_path .= "{$libjpeg_prefix};";
    $cmake_prefix_path .= "{$libpng_prefix};";
    $cmake_prefix_path .= "{$zlib_prefix};";
    $cmake_prefix_path .= "{$libwebp_prefix};";
    $lib = new Library('libjxl');
    $lib->withHomePage('https://github.com/ebiggers/libdeflate')
        ->withLicense('https://github.com/libjxl/libjxl/#BSD-3-Clause-1-ov-file', Library::LICENSE_BSD)
        ->withManual('https://github.com/libjxl/libjxl.git')
        ->withUrl('https://github.com/libjxl/libjxl/archive/refs/tags/v0.11.1.tar.gz')
        ->withFile('libjxl-v0.11.1.tar.gz')
        ->withPrefix($libjxl_prefix)
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
        mkdir -p build_dir
        cd build_dir
        cmake -S .. -B . \
        -DCMAKE_INSTALL_PREFIX={$libjxl_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DJPEGXL_ENABLE_FUZZERS=OFF  \
        -DBUILD_SHARED_LIBS=OFF \
        -DBUILD_STATIC_LIBS=ON \
        -DJPEGXL_ENABLE_DOXYGEN=OFF \
        -DJPEGXL_ENABLE_MANPAGES=OFF \
        -DJPEGXL_ENABLE_BENCHMARK=OFF \
        -DJPEGXL_ENABLE_EXAMPLES=OFF \
        -DJPEGXL_ENABLE_JNI=OFF \
        -DJPEGXL_STATIC=OFF \
        -DBUILD_TESTING=OFF \
        -DCMAKE_PREFIX_PATH="{$cmake_prefix_path}" \


        cmake --build . --config Release

        cmake --build . --config Release --target install
EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$libjxl_prefix}/lib/*.so.*
            rm -rf {$libjxl_prefix}/lib/*.so
EOF
        )
        ->withBinPath($libjxl_prefix . '/bin/')
        ->withPkgName('aom')
        ->withDependentLibraries(
            'brotli',
            'libgif',
            'libjpeg'
        ) ;

    $p->addLibrary($lib);
};


https://github.com/libjxl/libjxl
