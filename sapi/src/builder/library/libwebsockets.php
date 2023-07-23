<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libwebsockets_prefix = LIBWEBSOCKETS_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('libwebsockets');
    $lib->withHomePage('https://libwebsockets.org/')
        ->withLicense('https://github.com/warmcat/libwebsockets/blob/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://github.com/opencv/opencv.git')
        ->withFile('libwebsockets-v4.3.2.tar.gz')
        ->withDownloadScript(
            'libwebsockets',
            <<<EOF
             git clone -b v4.3.2 --depth=1 https://github.com/warmcat/libwebsockets.git
EOF
        )
        ->withPrefix($libwebsockets_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libwebsockets_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_C_FLAGS="-fpic" \
            -DOpenSSL_ROOT={$openssl_prefix}
EOF
        )
    ;

    $p->addLibrary($lib);
};
