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
    $svt_av1_prefix = SVT_AV1_PREFIX;
    $p->addLibrary(
        (new Library('libavif'))
            ->withHomePage('https://aomediacodec.github.io/av1-avif/')
            ->withLicense('https://github.com/AOMediaCodec/libavif/', Library::LICENSE_BSD)
            ->withManual('https://github.com/AOMediaCodec/libavif/ext/')
            ->withUrl('https://github.com/AOMediaCodec/libavif/archive/refs/tags/v0.11.1.tar.gz')
            ->withFile('libavif-v0.11.1.tar.gz')
            ->withPrefix($libavif_prefix)
            ->withBuildLibraryCached(true)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libavif_prefix)
            ->withConfigure(
                <<<EOF
            mkdir -p build
            cd build

            cmake ..  \
            -DCMAKE_INSTALL_PREFIX={$libavif_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DAVIF_BUILD_EXAMPLES=OFF \
            -DCMAKE_DISABLE_FIND_PACKAGE_libyuv=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_libsharpyuv=ON \
            -Dsvt_ROOT={$svt_av1_prefix} \
            -Daom_ROOT={$aom_prefix} \
            -Dlibgav1_ROOT={$libgav1_prefix} \
            -Ddav1d_ROOT={$dav1d_prefix} \
            -DAVIF_CODEC_AOM=ON \
            -DAVIF_CODEC_DAV1D=ON \
            -DAVIF_CODEC_LIBGAV1=ON \
            -DAVIF_CODEC_RAV1E=OFF \
            -DAVIF_CODEC_SVT=ON

            # -Dlibyuv_ROOT={$libyuv_prefix} \
            # -DLIBYUV_INCLUDE_DIR={$libyuv_prefix}/include \
            # -DLIBYUV_LIBRARY={$libyuv_prefix}/lib
            # -DLIBSHARPYUV_INCLUDE_DIR={$libwebp_prefix}/include \
            # -DLIBSHARPYUV_LIBRARY={$libwebp_prefix}/include


EOF
            )
            ->withPkgName('libavif')
            ->withDependentLibraries(
                'aom',
                'svt_av1',
                'libgav1',
                'dav1d',
            )
        #     'libyuv',  'libsharpyuv','rav1e'
        #   'libgav1' 依赖 absl
    );
};
