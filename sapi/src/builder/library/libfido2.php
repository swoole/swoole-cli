<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libfido2_prefix = LIBFIDO2_PREFIX;
    $lib = new Library('libfido2');
    $lib->withHomePage('https://developers.yubico.com/libfido2/')
        ->withLicense('https://developers.yubico.com/libfido2/', Library::LICENSE_BSD)
        ->withUrl('https://developers.yubico.com/libfido2/Releases/libfido2-1.13.0.tar.gz')
        ->withManual('https://developers.yubico.com/libfido2/')
        ->withPrefix($libfido2_prefix)
        ->withBuildScript(
            <<<EOF
            cmake -B build \
            -DCMAKE_INSTALL_PREFIX={$libfido2_prefix} \
            -DCMAKE_BUILD_TYPE=Release \
            -DBUILD_SHARED_LIBS=OFF \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_TOOLS=ON \
            -DFUZZ=OFF \
            -DNFC_LINUX=ON \
            -DUSE_HIDAPI=OFF \
            -DUSE_PCSC=OFF \
            -DUSE_WINHELLO=OFF

            make -C build install
EOF
        )
        ->withDependentLibraries('libcbor', 'openssl', 'zlib', 'libudev')
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
