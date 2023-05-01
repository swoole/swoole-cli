<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('zlib'))
            ->withHomePage('https://zlib.net/')
            ->withLicense('https://zlib.net/zlib_license.html', Library::LICENSE_SPEC)
            ->withUrl('https://udomain.dl.sourceforge.net/project/libpng/zlib/1.2.11/zlib-1.2.11.tar.gz')
            ->withPrefix(ZLIB_PREFIX)
            ->withConfigure('./configure --prefix=' . ZLIB_PREFIX . ' --static')
            ->withPkgName('zlib')
            ->depends('libxml2', 'bzip2')
    );
    $p->addExtension(
        (new Extension('zlib'))
            ->withHomePage('https://www.php.net/zlib')
            ->withOptions('--with-zlib --with-zlib-dir=' . ZLIB_PREFIX)
            ->depends('zlib')
    );
};
