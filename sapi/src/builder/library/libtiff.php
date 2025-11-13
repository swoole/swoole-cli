<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libtiff_prefix = LIBTIFF_PREFIX;
    $libgif_prefix = GIF_PREFIX;
    $lib = new Library('libtiff');
    $lib->withHomePage('http://www.libtiff.org/')
        ->withLicense('https://gitlab.com/libtiff/libtiff/-/blob/master/LICENSE.md', Library::LICENSE_SPEC)
        ->withUrl('http://download.osgeo.org/libtiff/tiff-4.5.0.tar.gz')
        ->withPrefix($libtiff_prefix)
        ->withConfigure(
            <<<EOF
            ./configure --help

            PACKAGES="zlib libjpeg libturbojpeg liblzma  libzstd libpng libjpeg libsharpyuv libwebp libwebpdecoder libwebpdemux libwebpmux"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) -I{$libgif_prefix}/include/ "\
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) -L{$libgif_prefix}/lib/ "\
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) -lgif "\
            ./configure \
            --prefix={$libtiff_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --disable-docs \
            --disable-tests \
            --disable-contrib \

EOF
        )
        ->withBinPath($libtiff_prefix . '/bin')
        ->withPkgName('libtiff-4')
        ->withDependentLibraries('zlib', 'libjpeg', 'liblzma', 'libzstd', 'libpng', 'libgif', );

    $p->addLibrary($lib);
};
