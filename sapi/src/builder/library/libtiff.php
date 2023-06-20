<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libtiff_prefix = LIBTIFF_PREFIX;
    $lib = new Library('libtiff');
    $lib->withHomePage('http://www.libtiff.org/')
        ->withLicense('https://gitlab.com/libtiff/libtiff/-/blob/master/LICENSE.md', Library::LICENSE_SPEC)
        ->withUrl('http://download.osgeo.org/libtiff/tiff-4.5.0.tar.gz')
        ->withPrefix($libtiff_prefix)
        ->withConfigure(
            <<<EOF
            ./configure --help

            PACKAGES="zlib libjpeg libturbojpeg liblzma  libzstd "
            CPPFLAGS=$(pkg-config  --cflags-only-I --static \$PACKAGES ) \
            LDFLAGS=$(pkg-config   --libs-only-L   --static \$PACKAGES ) \
            LIBS=$(pkg-config      --libs-only-l   --static \$PACKAGES ) \
            ./configure \
            --prefix={$libtiff_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --disable-docs \
            --disable-tests \
            --disable-webp

EOF
        )
        ->withBinPath($libtiff_prefix . '/bin')
        ->withPkgName('libtiff-4')
        ->withDependentLibraries('zlib', 'libjpeg', 'liblzma', 'libzstd')
    ;

    $p->addLibrary($lib);
};
