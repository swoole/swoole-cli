<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $openjpeg_prefix = OPENJPEG_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $liblcms2_prefix = LCMS2_PREFIX;
    $libpng_prefix = PNG_PREFIX;

    $libzstd_prefix = LIBZSTD_PREFIX;
    $liblz4_prefix = LIBLZ4_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;


    $lib = new Library('openjpeg');
    $lib->withHomePage('https://www.openjpeg.org/')
        ->withLicense('https://github.com/uclouvain/openjpeg/blob/master/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://github.com/uclouvain/openjpeg/blob/master/INSTALL.md')
        ->withFile('openjpeg-latest.tar.gz')
        ->withDownloadScript(
            'openjpeg',
            <<<EOF
        git clone -b master --depth=1  https://github.com/uclouvain/openjpeg.git
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip  nasm yasm
            pip3 install meson
EOF
        )
        ->withPrefix($openjpeg_prefix)
        ->withConfigure(
            <<<EOF
             mkdir -p build
             cd build

             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$openjpeg_prefix} \
            -DCMAKE_INSTALL_LIBDIR={$openjpeg_prefix}/lib \
            -DCMAKE_INSTALL_INCLUDEDIR={$openjpeg_prefix}/include \
            -DCMAKE_C_STANDARD=11 \
            -DCMAKE_POLICY_DEFAULT_CMP0075=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_TESTING=OFF \
            -DBUILD_JPIP=ON \
            -DCMAKE_PREFIX_PATH="{$zlib_prefix};{$libtiff_prefix};{$liblcms2_prefix};{$libpng_prefix}" \
            -DBUILD_CODEC=OFF

EOF
        )
        ->withPkgName('libopenjp2')
        ->withBinPath($openjpeg_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'libpng', 'libtiff', 'lcms2');

    $p->addLibrary($lib);
};

/*
# USE OpenJPEG

find_package(OpenJPEG REQUIRED)
include_directories(${OPENJPEG_INCLUDE_DIRS})
add_executable(myapp myapp.c)
target_link_libraries(myapp ${OPENJPEG_LIBRARIES})

 */
