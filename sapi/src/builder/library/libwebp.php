<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $libwebp_prefix = WEBP_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $libgif_prefix = GIF_PREFIX;
    $libzip_prefix =ZLIB_PREFIX;
    $libpng_prefix = PNG_PREFIX;
    $sdl_prefix = SDL2_PREFIX;
    $p->addLibrary(
        (new Library('libwebp'))
            ->withHomePage('https://chromium.googlesource.com/webm/libwebp')
            ->withManual('https://chromium.googlesource.com/webm/libwebp/+/HEAD/doc/building.md')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
            ->withUrl('https://github.com/webmproject/libwebp/archive/refs/tags/v1.3.2.tar.gz')
            ->withFile('libwebp-v1.3.2.tar.gz')
            ->withPrefix($libwebp_prefix)
            ->withBuildScript(
                <<<EOF
            mkdir -p build
            cd build
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libwebp_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DWEBP_BUILD_LIBWEBPMUX=ON \
            -DWEBP_BUILD_ANIM_UTILS=ON \
            -DWEBP_BUILD_CWEBP=ON \
            -DWEBP_BUILD_DWEBP=ON \
            -DWEBP_BUILD_GIF2WEBP=ON \
            -DWEBP_BUILD_IMG2WEBP=ON \
            -DWEBP_BUILD_VWEBP=ON \
            -DWEBP_BUILD_WEBPMUX=ON \
            -DWEBP_BUILD_WEBPINFO=ON \
            -DGIF_INCLUDE_DIR={$libgif_prefix}/include/ \
            -DGIF_LIBRARY={$libgif_prefix}/lib/libgif.a \
            -DCMAKE_DISABLE_FIND_PACKAGE_OpenGL=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_TIFF=ON \
            -DCMAKE_PREFIX_PATH="{$libzip_prefix};{$libpng_prefix};{$libjpeg_prefix};{$sdl_prefix}"

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
            )
            ->withConfigure(
                <<<EOF

                ./autogen.sh
                ./configure --help

                PACKAGES='libpng libjpegsdl2 '
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES ) -I{$libgif_prefix}/include/ " \
                LDFLAGS="$(pkg-config --libs-only-L      --static \$PACKAGES ) -L{$libgif_prefix}/lib/ " \
                LIBS="$(pkg-config --libs-only-l         --static \$PACKAGES ) -lgif" \
                ./configure \
                --prefix={$libwebp_prefix} \
                --enable-shared=no \
                --enable-static=yes \
                --enable-everything \
                --disable-tiff

EOF
            )

            ->withPkgName('libsharpyuv')
            ->withPkgName('libwebp')
            ->withPkgName('libwebpdecoder')
            ->withPkgName('libwebpdemux')
            ->withPkgName('libwebpmux')
            ->withBinPath($libwebp_prefix . '/bin/')
            ->withDependentLibraries(
                'libpng',
                'libjpeg',
                'libgif',
                'sdl2'
            )
    );
};
