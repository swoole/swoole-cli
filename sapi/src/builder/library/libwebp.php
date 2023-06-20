<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libpng_prefix = PNG_PREFIX;
    $libwebp_prefix = WEBP_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $libgif_prefix = GIF_PREFIX;
    $p->addLibrary(
        (new Library('libwebp'))
            ->withHomePage('https://chromium.googlesource.com/webm/libwebp')
            ->withManual('https://chromium.googlesource.com/webm/libwebp/+/HEAD/doc/building.md')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withPrefix($libwebp_prefix)
            ->withConfigure(
                <<<EOF
                ./autogen.sh
                ./configure --help
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static libpng libjpeg )" \
                LDFLAGS="$(pkg-config --libs-only-L      --static libpng libjpeg )" \
                LIBS="$(pkg-config --libs-only-l         --static libpng libjpeg )" \
                ./configure --prefix={$libwebp_prefix} \
                --enable-static --disable-shared \
                --enable-libwebpdecoder \
                --enable-libwebpextras \
                --with-pngincludedir={$libpng_prefix}/include \
                --with-pnglibdir={$libpng_prefix}/lib \
                --with-jpegincludedir={$libjpeg_prefix}/include \
                --with-jpeglibdir={$libjpeg_prefix}/lib \
                --with-gifincludedir={$libgif_prefix}/include \
                --with-giflibdir={$libgif_prefix}/lib \
                --disable-tiff

EOF
            )
            ->withPkgName('libwebp')
            ->withLdflags('-L' . $libwebp_prefix . '/lib -lwebpdemux -lwebpmux')
            ->withBinPath($libwebp_prefix . '/bin/')
            ->withDependentLibraries('libpng', 'libjpeg', 'libgif')
    );
};
