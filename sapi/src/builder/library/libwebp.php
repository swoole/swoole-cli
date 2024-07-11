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
            ->withUrl('https://github.com/webmproject/libwebp/archive/refs/tags/v1.3.2.tar.gz')
            ->withFile('libwebp-v1.3.2.tar.gz')
            ->withFileHash('md5', '827d510b73c73fca3343140556dd2943')
            ->withPrefix($libwebp_prefix)
            ->withConfigure(
                <<<EOF

                ./autogen.sh
                ./configure --help

                PACKAGES='libpng libjpeg '
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
            ->withDependentLibraries('libpng', 'libjpeg', 'libgif')
    );
};
