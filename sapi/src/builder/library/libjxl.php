<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libjxl_prefix = LIBJXL_PREFIX;
    $lib = new Library('libjxl');
    $lib->withHomePage('https://github.com/libjxl/libjxl.git')
        ->withLicense('https://github.com/libjxl/libjxl/blob/main/LICENSE', Library::LICENSE_BSD)
        ->withUrl('https://github.com/libjxl/libjxl/archive/refs/tags/v0.8.1.tar.gz')
        ->withManual('https://github.com/libjxl/libjxl/blob/main/BUILDING.md')
        ->withFile('libjpegxl-v0.8.1.tar.gz')
        ->withPrefix($libjxl_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libjxl_prefix)
        ->withBuildScript(
            <<<EOF

        ## 会自动 下载依赖 ，如网速不佳，请在环境变量里设置代理地址，用于加速下载
        git init -b main .
        git add ./.gitmodules

        git -C . submodule update --init --recursive --depth 1 --recommend-shallow

        sh deps.sh

        git submodule update --init --recursive
        exit 0
        mkdir -p build
        cd build
        cmake -DJPEGXL_STATIC=true \
        -DCMAKE_BUILD_TYPE=Release \
        -DBUILD_SHARED_LIBS=OFF \
        -DBUILD_TESTING=OFF \
        -DCMAKE_INSTALL_PREFIX={$libjxl_prefix} \
         ..

        cmake --build . -- -j$(nproc)
        cmake --install .
EOF
        )
        ->withPkgName('libjxl');

    $p->addLibrary($lib);
};
