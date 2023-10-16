<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libsharpyuv_prefix = LIBSHARPYUV_PREFIX;
    $libwebp_prefix = WEBP_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $libgif_prefix = GIF_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $libpng_prefix = PNG_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $sdl2_prefix = SDL2_PREFIX;
    $p->addLibrary(
        (new Library('libsharpyuv'))
            ->withHomePage('https://chromium.googlesource.com/webm/libwebp')
            ->withManual('https://github.com/AOMediaCodec/libavif/tree/main/ext/libsharpyuv.cmd')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withDownloadScript(
                'libwebp',
                <<<EOF
            git clone -b v1.3.0  https://chromium.googlesource.com/webm/libwebp
EOF
            )
            ->withFile('libsharpyuv-v1.3.0.tar.gz')
            ->withPrefix($libsharpyuv_prefix)
            ->withCleanBuildDirectory()
            ->withBuildScript(
                <<<EOF
                mkdir -p build
                cd build
                cmake -G Ninja .. \
                -DCMAKE_INSTALL_PREFIX={$libsharpyuv_prefix} \
                -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
                -DBUILD_SHARED_LIBS=OFF \
                -DCMAKE_BUILD_TYPE=Release \
                -DZLIB_ROOT={$zlib_prefix} \
                -DPNG_ROOT={$libpng_prefix} \
                -DJPEG_ROOT={$libjpeg_prefix} \
                -DGIF_ROOT={$libgif_prefix}

                -DCMAKE_DISABLE_FIND_PACKAGE_OpenGL=ON

                # -DTIFF_ROOT={$libtiff_prefix} \
                # -DOpenGL_ROOT={} \
                # -DGLUT_ROOT={} \
                # -DSDL_ROOT={$sdl2_prefix} \

                ninja sharpyuv
                ninja install

EOF
            )
            ->withPkgName('libsharpyuv')
            ->withBinPath($libwebp_prefix . '/bin/')
            ->withDependentLibraries('libpng', 'libjpeg', 'libgif')
    );
};