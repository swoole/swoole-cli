<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('zlib'))
            ->withUrl('https://udomain.dl.sourceforge.net/project/libpng/zlib/1.2.11/zlib-1.2.11.tar.gz')
            ->withPrefix('/usr/zlib')
            ->withConfigure('./configure --prefix=/usr/zlib --static')
            ->withHomePage('https://zlib.net/')
            ->withLicense('https://zlib.net/zlib_license.html', Library::LICENSE_SPEC)
            ->withPkgName('zlib')
            ->depends('libxml2', 'bzip2')
    );
    $p->addExtension((new Extension('zlib'))->withOptions('--with-zlib --with-zlib-dir=/usr/zlib'));
};
