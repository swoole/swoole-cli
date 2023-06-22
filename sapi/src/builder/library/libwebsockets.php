<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libwebsockets_prefix = LIBWEBSOCKETS_PREFIX;
    $lib = new Library('libwebsockets');
    $lib->withHomePage('https://libwebsockets.org/')
        ->withLicense('https://github.com/warmcat/libwebsockets/blob/main/LICENSE', Library::LICENSE_SPEC)
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
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
        mkdir build && cd build
        cmake -DCMAKE_INSTALL_PREFIX:PATH={$libwebsockets_prefix} -DCMAKE_C_FLAGS="-fpic" ..
EOF
        )
    ;

    $p->addLibrary($lib);
};
