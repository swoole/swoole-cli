<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('libsodium'))
            // ISC License, like BSD
            ->withLicense('https://en.wikipedia.org/wiki/ISC_license', Library::LICENSE_SPEC)
            ->withHomePage('https://doc.libsodium.org/')
            ->withUrl('https://download.libsodium.org/libsodium/releases/libsodium-1.0.18.tar.gz')
            ->withPrefix('/usr/libsodium')
            ->withConfigure('./configure --prefix=/usr/libsodium --enable-static --disable-shared')
            ->withPkgName('libsodium')
    );
    $p->addExtension((new Extension('sodium'))->withOptions('--with-sodium'));
};
