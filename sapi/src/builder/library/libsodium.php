<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('libsodium'))
            // ISC License, like BSD
            ->withLicense('https://en.wikipedia.org/wiki/ISC_license', Library::LICENSE_SPEC)
            ->withHomePage('https://doc.libsodium.org/')
            ->withUrl('https://download.libsodium.org/libsodium/releases/libsodium-1.0.21.tar.gz')
            ->withFileHash('md5', 'ecd60ebc2c916133db2f6b3b2e9e775d')
            ->withPrefix(LIBSODIUM_PREFIX)
            ->withConfigure('./configure --prefix=' . LIBSODIUM_PREFIX . ' --enable-static --disable-shared')
            ->withPkgName('libsodium')
    );
};
