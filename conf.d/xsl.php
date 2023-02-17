<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('libxslt'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxslt/-/archive/v1.1.34/libxslt-v1.1.34.tar.gz')
            ->withPrefix(LIBXSLT_PREFIX)
            ->withConfigure('./autogen.sh && ./configure --prefix=' . LIBXSLT_PREFIX . '--enable-static=yes --enable-shared=no')
            ->withLicense('http://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->withPkgName('libexslt libxslt')
            ->depends('libxml2', 'libiconv')
    );
    $p->addExtension((new Extension('xsl'))->withOptions('--with-xsl')->depends('libxslt'));
};
