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
            ->withUrl('https://github.com/AOMediaCodec/libavif/archive/refs/tags/v0.11.1.tar.gz')
            ->withFile('libavif-v0.11.1.tar.gz')
            ->withHomePage('https://aomediacodec.github.io/av1-avif/')
            ->withLicense('https://github.com/AOMediaCodec/libavif/blob/main/LICENSE', Library::LICENSE_SPEC)
            ->withManual('https://github.com/AOMediaCodec/libavif/ext/')
            ->withPrefix($libavif_prefix)
            ->withBuildLibraryCached(false)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libavif_prefix)
            ->withConfigure(
                <<<EOF
            mkdir -p build
            cd build

            cmake ..  \
            -DCMAKE_INSTALL_PREFIX={$libavif_prefix} \
            -DAVIF_BUILD_EXAMPLES=OFF \
            -DCMAKE_DISABLE_FIND_PACKAGE_libyuv=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_libsharpyuv=ON \
            -Daom_ROOT={$aom_prefix} \
            -Dsvt_ROOT={$svt_av1_prefix} \
            -DBUILD_SHARED_LIBS=OFF \
            -DAVIF_CODEC_AOM=ON \
            -DAVIF_CODEC_DAV1D=OFF \
            -DAVIF_CODEC_LIBGAV1=OFF \
            -DAVIF_CODEC_RAV1E=OFF \
            -DAVIF_CODEC_SVT=ON
            # -Ddav1d_ROOT={$dav1d_prefix} \
            # -Dlibgav1_ROOT={$libgav1_prefix} \
            # -Dlibyuv_ROOT={$libyuv_prefix} \
            # -DLIBYUV_INCLUDE_DIR={$libyuv_prefix}/include \
            # -DLIBYUV_LIBRARY={$libyuv_prefix}/lib
            # -DLIBSHARPYUV_INCLUDE_DIR={$libwebp_prefix}/include \
            # -DLIBSHARPYUV_LIBRARY={$libwebp_prefix}/include


EOF
            )
            ->withPkgName('libavif')
            ->withDependentLibraries('aom', 'svt_av1') #'dav1d', 'libgav1',  'libyuv',  'libsharpyuv',
    );
};
