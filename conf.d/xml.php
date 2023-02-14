<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        // MUST be in the /usr directory
        (new Library('libxml2',"/usr/libxml2"))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxml2/-/archive/v2.9.10/libxml2-v2.9.10.tar.gz')
            ->withConfigure(<<<EOF
./autogen.sh && ./configure --prefix=/usr/libxml2 --with-iconv=/usr/libiconv --enable-static=yes --enable-shared=no --without-python
EOF
            )
            ->withPkgName('libxml-2.0')
            ->withLicense('https://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->depends('libiconv')
    );
    $p->addExtension((new Extension('xml'))
        ->withOptions('--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml=/usr/libxml2')
        ->depends('libxml2')
    );
};
