<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $aom_prefix = AOM_PREFIX;
    $lib = new Library('aom');
    $lib->withHomePage('https://aomedia.googlesource.com/aom')
        ->withLicense('https://aomedia.googlesource.com/aom/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://aomedia.googlesource.com/aom')
        ->withUrl('https://aomedia.googlesource.com/aom')
        ->withFile('aom-v3.6.1.tar.gz')
        ->withDownloadScript(
            'aom',
            <<<EOF
            git clone -b v3.6.1 --depth=1  https://aomedia.googlesource.com/aom
EOF
        )
        ->withPrefix($aom_prefix)
        ->withBuildLibraryCached(true)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($aom_prefix)
        ->withConfigure(
            <<<EOF
            mkdir -p build_dir
            cd build_dir
             cmake ..  \
            -DCMAKE_INSTALL_PREFIX={$aom_prefix} \
            -DCMAKE_C_STANDARD=11 \
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
