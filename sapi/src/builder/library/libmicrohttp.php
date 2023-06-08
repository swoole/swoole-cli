<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmicrohttp_prefix = LIBMICROHTTP_PREFIX;

    $p->addLibrary(
        (new Library('libmicrohttp'))
            ->withHomePage('https://www.gnu.org/software/libmicrohttpd/')
            ->withLicense('https://www.gnu.org/software/libmicrohttpd/#license', Library::LICENSE_LGPL)
            ->withUrl('https://ftpmirror.gnu.org/libmicrohttpd/libmicrohttpd-latest.tar.gz')
            ->withFile('libmicrohttpd-latest.tar.gz')
            ->withBinPath($libmicrohttp_prefix . '/bin/')
            ->depends('openssl', 'readline')
    );
};
