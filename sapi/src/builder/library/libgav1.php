<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libgav1_prefix = LIBGAV1_PREFIX;
    $p->addLibrary(
        (new Library('libgav1'))
            ->withHomePage('https://chromium.googlesource.com/codecs/libgav1')
            ->withLicense(
                'https://chromium.googlesource.com/codecs/libgav1/+/refs/heads/main/LICENSE',
                Library::LICENSE_APACHE2
            )
            ->withFile('libgav1-latest.tar.gz')
            ->withManual('https://chromium.googlesource.com/codecs/libgav1/+/refs/heads/main')
            ->withDownloadScript(
                'libgav1',
                <<<EOF
                git clone -b main --depth 1  https://chromium.googlesource.com/codecs/libgav1
                mkdir -p libgav1/third_party/abseil-cpp
                git clone -b 20220623.0 --depth 1 https://github.com/abseil/abseil-cpp.git libgav1/third_party/abseil-cpp
EOF
            )
            ->withPrefix($libgav1_prefix)
            ->withConfigure(
                <<<EOF
                mkdir -p build
                cd build
                # 查看更多选项
                # cmake .. -LH
                cmake -G "Unix Makefiles" .. \
                -DCMAKE_INSTALL_PREFIX={$libgav1_prefix} \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_SHARED_LIBS=OFF  \
                -DBUILD_STATIC_LIBS=ON \
                -DLIBGAV1_ENABLE_TESTS=OFF \
                -DLIBGAV1_ENABLE_EXAMPLES=OFF \
                -DLIBGAV1_THREADPOOL_USE_STD_MUTEX=1

EOF
            )
            ->withPkgName('libgav1')
            ->withBinPath($libgav1_prefix . '/bin/')
        //->withDependentLibraries('absl') //测试需要它，更多请查看文档
    );
};
