<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libopencc_prefix = LIBOPENCC_PREFIX;
    //中文繁简体转换
    $lib = new Library('libopencc');
    $lib->withHomePage('https://opencc.byvoid.com/')
        ->withLicense('https://github.com/BYVoid/OpenCC/blob/master/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/BYVoid/OpenCC.git')
        ->withFile('libopencc-latest.tar.gz')
        ->withDownloadScript(
            'OpenCC',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/BYVoid/OpenCC.git
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add python3
EOF
        )
        ->withPrefix($libopencc_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libopencc_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_DOCUMENTATION=OFF \
            -DENABLE_GTEST=OFF \
            -DENABLE_BENCHMARK=OFF \
            -DENABLE_DARTS=ON \
            -DBUILD_PYTHON=OFF \
            -DUSE_SYSTEM_DARTS=OFF

            cmake --build . --config Release --target install

EOF
        )

        ->withPkgName('opencc')
        ->withBinPath($libopencc_prefix . '/bin/')
    ;

    $p->addLibrary($lib);
};
