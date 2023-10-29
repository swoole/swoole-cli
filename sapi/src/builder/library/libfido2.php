<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libfido2_prefix = LIBFIDO2_PREFIX;
    $lib = new Library('libfido2');
    $lib->withHomePage('https://developers.yubico.com/libfido2/')
        ->withLicense('https://developers.yubico.com/libfido2/', Library::LICENSE_BSD)
        ->withManual('https://developers.yubico.com/libfido2/')
        //->withUrl('https://developers.yubico.com/libfido2/Releases/libfido2-1.13.0.tar.gz')
            ->withFile('libfido2-latest.tar.gz')
        ->withDownloadScript(
            'libfido2',
            <<<EOF
        git clone -b main --depth=1 https://github.com/Yubico/libfido2.git
EOF
        )
        ->withPrefix($libfido2_prefix)
        ->withBuildScript(
            <<<EOF
            mkdir -p build
            cd build
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libfido2_prefix} \
            -DCMAKE_BUILD_TYPE=Release \
            -DBUILD_SHARED_LIBS=OFF \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_EXAMPLES=OFF \
            -DBUILD_MANPAGES=OFF \
            -DBUILD_TOOLS=ON \
            -DFUZZ=OFF \
            -DNFC_LINUX=OFF \
            -DUSE_HIDAPI=OFF \
            -DUSE_PCSC=OFF \
            -DUSE_WINHELLO=OFF

            cmake --build . --config Release

            cmake --build . --config Release --target install
EOF
        )
        ->withDependentLibraries(
            'libcbor',
            'openssl',
            'zlib',
            //'libudev'
        )
    ;

    $p->addLibrary($lib);
};
