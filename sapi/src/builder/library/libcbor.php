<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libcbor_prefix = LIBCBOR_PREFIX;
    $lib = new Library('libcbor');
    $lib->withHomePage('https://libcbor.readthedocs.io/en/latest/')
        ->withLicense('https://github.com/PJK/libcbor/blob/master/LICENSE.md', Library::LICENSE_MIT)
        ->withManual('https://libcbor.readthedocs.io/en/latest/getting_started.html#building-installing-libcbor')
        ->withManual('https://github.com/PJK/libcbor/blob/master/doc/source/getting_started.rst')
        ->withFile('libcbor-latest.tar.gz')
        ->withDownloadScript(
            'libcbor',
            <<<EOF
                git clone -b master --depth=1 https://github.com/PJK/libcbor.git
EOF
        )
        ->withPrefix($libcbor_prefix)
        ->withBuildScript(
            <<<EOF
            mkdir -p build
            cd build
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libcbor_prefix} \
            -DCMAKE_BUILD_TYPE=Release \
            -DBUILD_SHARED_LIBS=OFF \
            -DBUILD_STATIC_LIBS=ON \
            -DHUGE_FUZZ=OFF \
            -DWITH_TESTS=OFF

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('libcbor')
    ;

    $p->addLibrary($lib);
};
