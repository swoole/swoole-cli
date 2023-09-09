<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $cpu_features_prefix = CPU_FEATURES_PREFIX;

    $lib = new Library('cpu_features');
    $lib->withHomePage('https://github.com/google/cpu_features.git')
        ->withLicense('https://github.com/google/cpu_features/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/google/cpu_features.git')
        /* 下载依赖库源代码方式二 start */
        ->withFile('cpu_features-latest.tar.gz')
        ->withDownloadScript(
            'cpu_features',
            <<<EOF
                git clone -b main  --depth=1 https://github.com/google/cpu_features.git
EOF
        )
        ->withPrefix($cpu_features_prefix)
        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build

             cmake -S. .. \
            -DCMAKE_INSTALL_PREFIX={$cpu_features_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_TESTING=OFF

            cmake --build build --config Release -j
            cmake --build build --config Release --target install -v
            # cmake --build build --config Release --target install -v -- DESTDIR=install

EOF
        )
        ->withBinPath($cpu_features_prefix . '/bin/');

    $p->addLibrary($lib);

};
