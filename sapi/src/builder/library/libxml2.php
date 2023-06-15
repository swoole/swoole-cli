<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libxml2_prefix = LIBXML2_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libxml2'))
            ->withHomePage('https://gitlab.gnome.org/GNOME/libxml2/')
            ->withManual('https://gitlab.gnome.org/GNOME/libxml2/-/wikis')
            ->withLicense('https://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->withUrl('https://gitlab.gnome.org/GNOME/libxml2/-/archive/v2.9.10/libxml2-v2.9.10.tar.gz')
            ->withPrefix($libxml2_prefix)
            ->withConfigure(
                <<<EOF
                    ./autogen.sh
                    ./configure --help
                    ./configure \
                    --prefix=$libxml2_prefix \
                    --with-iconv=$iconv_prefix \
                    --enable-static=yes \
                    --enable-shared=no \
                    --without-python \
                    --without-zlib \
                    --without-lzma \
                    --without-debug \
                    --without-icu

EOF
            )
            ->withPkgName('libxml-2.0')
            ->withDependentLibraries('libiconv')
            ->withBinPath($libxml2_prefix . '/bin/')
    );
};
