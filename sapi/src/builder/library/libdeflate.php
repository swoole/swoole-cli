<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libdeflate_prefix = LIBDEFLATE_PREFIX;
    $lib = new Library('libdeflate');
    $lib->withHomePage('https://github.com/ebiggers/libdeflate.git')
        ->withLicense('https://github.com/ebiggers/libdeflate/blob/master/COPYING', Library::LICENSE_MIT)
        ->withManual('https://github.com/ebiggers/libdeflate.git')
        ->withFile('libdeflate-latest.tar.gz')
        ->withDownloadScript(
            'libdeflate',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/ebiggers/libdeflate.git
EOF
        )
        ->withPrefix($libdeflate_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build

             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libdeflate_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )



        ->withPkgName('libdeflate')
        ->withBinPath($libdeflate_prefix . '/bin/')
    ;

    $p->addLibrary($lib);

};
