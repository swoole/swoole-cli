<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libavif_prefix = LIBAVIF_PREFIX;
    $libyuv_prefix = LIBYUV_PREFIX;
    $dav1d_prefix = DAV1D_PREFIX;
    $libgav1_prefix = LIBGAV1_PREFIX;
    $aom_prefix = AOM_PREFIX;
    $libwebp_prefix = WEBP_PREFIX;
    $p->addLibrary(
        (new Library('libavif'))
            ->withUrl('https://github.com/AOMediaCodec/libavif/archive/refs/tags/v0.11.1.tar.gz')
            ->withFile('libavif-v0.11.1.tar.gz')
            ->withHomePage('https://aomediacodec.github.io/av1-avif/')
            ->withLicense('https://github.com/AOMediaCodec/libavif/blob/main/LICENSE', Library::LICENSE_SPEC)
            ->withManual('https://github.com/AOMediaCodec/libavif/ext/')
            ->withPrefix($libavif_prefix)
            ->withConfigure(
                <<<EOF
            mkdir -p build
            cd build

            cmake ..  \
            -DCMAKE_INSTALL_PREFIX={$libavif_prefix} \
            -DAVIF_BUILD_EXAMPLES=OFF \
            -Dlibyuv_ROOT={$libyuv_prefix} \
            -Ddav1d_ROOT={$dav1d_prefix} \
            -Dlibgav1_ROOT={$libgav1_prefix} \
            -Daom_ROOT={$libgav1_prefix} \
            -Dsvt_ROOT={$libgav1_prefix} \
            -DBUILD_SHARED_LIBS=OFF \
            -DAVIF_CODEC_AOM=ON \
            -DAVIF_CODEC_DAV1D=ON \
            -DAVIF_CODEC_LIBGAV1=ON \
            -DAVIF_CODEC_RAV1E=OFF \
            -DAVIF_CODEC_SVT=ON \
            -DLIBYUV_INCLUDE_DIR={$libyuv_prefix}/include \
            -DLIBYUV_LIBRARY={$libyuv_prefix}/lib
            # -DLIBSHARPYUV_INCLUDE_DIR={$libwebp_prefix}/include \
            # -DLIBSHARPYUV_LIBRARY={$libwebp_prefix}/include


EOF
            )
            ->withPkgName('libavif')
            ->withDependentLibraries('libwebp', 'dav1d', 'aom', 'libgav1', 'svt_av1', 'libyuv') # ,  'libsharpyuv',
    );
    $p->withVariable('LIBS', '$LIBS -lbrotli');
};
