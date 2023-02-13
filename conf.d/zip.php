<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    // MUST be in the /usr directory
    $p->addLibrary(
        (new Library('zip'))
            ->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
            ->withConfigure('cmake . -DENABLE_ZSTD=OFF -DENABLE_LZMA=OFF -DBUILD_SHARED_LIBS=OFF -DOPENSSL_USE_STATIC_LIBS=TRUE -DCMAKE_INSTALL_PREFIX=/usr')
            ->withPkgName('libzip')
            ->withHomePage('https://libzip.org/')
            ->withLicense('https://libzip.org/license/', Library::LICENSE_BSD)
    );
    $p->addExtension((new Extension('zip'))->withOptions('--with-zip'));
};
