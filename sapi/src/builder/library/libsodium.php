<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libsodium_prefix = LIBSODIUM_PREFIX;
    $p->addLibrary(
        (new Library('libsodium'))
            // ISC License, like BSD
            ->withLicense('https://en.wikipedia.org/wiki/ISC_license', Library::LICENSE_SPEC)
            ->withHomePage('https://doc.libsodium.org/')
            ->withUrl('https://download.libsodium.org/libsodium/releases/libsodium-1.0.18.tar.gz')
            ->withFileHash('md5','3ca9ebc13b6b4735acae0a6a4c4f9a95')
            ->withPrefix($libsodium_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libsodium_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
            )
            ->withPkgName('libsodium')
    );
};
