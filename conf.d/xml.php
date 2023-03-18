<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $libxml2_prefix = LIBXML2_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libxml2'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxml2/-/archive/v2.9.10/libxml2-v2.9.10.tar.gz')
            ->withPrefix($libxml2_prefix)
            ->withConfigure(
                <<<EOF
./autogen.sh && ./configure --prefix=$libxml2_prefix --with-iconv=$iconv_prefix --enable-static=yes --enable-shared=no --without-python
EOF
            )
            ->withPkgName('libxml-2.0')
            ->withLicense('https://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->depends('libiconv')
            ->withBinPath($libxml2_prefix . '/bin/')
    );
    $p->addExtension(
        (new Extension('xml'))
        ->withOptions('--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml')
        ->depends('libxml2')
    );
};
