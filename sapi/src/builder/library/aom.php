<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $aom_prefix = AOM_PREFIX;
    $lib = new Library('aom');
    $lib->withHomePage('https://aomedia.googlesource.com/aom')
        ->withLicense('https://aomedia.googlesource.com/aom/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://aomedia.googlesource.com/aom')
        ->withManual('https://aomedia.googlesource.com/aom/+/refs/tags/v3.10.0')
        ->withUrl('https://aomedia.googlesource.com/aom/+archive/c2fe6bf370f7c14fbaf12884b76244a3cfd7c5fc.tar.gz')
        ->withFile('aom-v3.10.0.tar.gz')
        ->withPrefix($aom_prefix)
        ->withUntarArchiveCommand('tar-default')
        ->withBuildCached(false)
        ->withConfigure(
            <<<EOF
            mkdir -p build_dir
            cd build_dir
             cmake -S .. -B . \
            -DCMAKE_INSTALL_PREFIX={$aom_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DCMAKE_C_STANDARD=11 \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DENABLE_DOCS=OFF \
            -DENABLE_EXAMPLES=OFF \
            -DENABLE_TESTS=OFF \
            -DENABLE_TOOLS=ON
EOF
        )
        ->withBinPath($aom_prefix . '/bin/')
        ->withPkgName('aom');

    $p->addLibrary($lib);
};
