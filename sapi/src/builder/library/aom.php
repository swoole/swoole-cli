<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $aom_prefix = AOM_PREFIX;
    $lib = new Library('aom');
    $lib->withHomePage('https://aomedia.googlesource.com/aom')
        ->withLicense('https://aomedia.googlesource.com/aom/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://aomedia.googlesource.com/aom')
        ->withFile('aom.tar.gz')
        ->withDownloadScript(
            'aom',
            <<<EOF
            git clone -b main --depth=1  https://aomedia.googlesource.com/aom
EOF
        )
        ->withPrefix($aom_prefix)
        ->withConfigure(
            <<<EOF
            mkdir -p build
            cd build
             cmake ..  \
            -DCMAKE_INSTALL_PREFIX={$aom_prefix} \
            -DCMAKE_C_STANDARD=11 \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DENABLE_DOCS=0 \
            -DENABLE_TESTS=0
EOF
        )
        ->withBinPath($aom_prefix . '/bin/')
        ->withPkgName('aom');

    $p->addLibrary($lib);
};
