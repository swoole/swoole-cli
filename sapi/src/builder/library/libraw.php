<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $link_cpp = $p->getOsType() == 'macos' ? '-lc++' : '-lstdc++';
    $libraw_prefix = LIBRAW_PREFIX;
    $lib = new Library('libraw');
    $lib->withHomePage('https://www.libraw.org/about')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://www.libraw.org/data/LibRaw-0.21.4.tar.gz')
        ->withPrefix($libraw_prefix)
        ->withConfigure(
            <<<EOF
            ./configure --help
            echo {$link_cpp}

            set -uex
            package_names="zlib libjpeg libturbojpeg lcms2"

            CPPFLAGS="\$(pkg-config  --cflags-only-I --static \$package_names )" \
            LDFLAGS="\$(pkg-config   --libs-only-L   --static \$package_names )" \
            LIBS="\$(pkg-config      --libs-only-l   --static \$package_names ) {$link_cpp}" \
            ./configure \
            --prefix={$libraw_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-jpeg \
            --enable-zlib \
            --enable-lcms \
            --disable-jasper  \
            --disable-openmp

EOF
        )
        ->withPkgName('libraw')
        ->withPkgName('libraw_r')
        ->withBinPath($libraw_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'libjpeg', 'lcms2');

    $p->addLibrary($lib);
};
