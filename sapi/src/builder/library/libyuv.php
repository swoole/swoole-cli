<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libyuv_prefix = LIBYUV_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $p->addLibrary(
        (new Library('libyuv'))
            ->withUrl('https://chromium.googlesource.com/libyuv/libyuv')
            ->withHomePage('https://chromium.googlesource.com/libyuv/libyuv')
            ->withLicense(
                'https://chromium.googlesource.com/libyuv/libyuv/+/refs/heads/main/LICENSE',
                Library::LICENSE_SPEC
            )
            ->withManual('https://chromium.googlesource.com/libyuv/libyuv/+/HEAD/docs/getting_started.md')
            ->withDownloadScript(
                'libyuv',
                <<<EOF
            git clone -b main --depth=1 https://chromium.googlesource.com/libyuv/libyuv
EOF
            )
            ->withPrefix($libyuv_prefix)
            ->withBuildScript(
                <<<EOF
                mkdir -p  build
                cd build
                cmake -G Ninja  .. \
                -DCMAKE_INSTALL_PREFIX={$libyuv_prefix} \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_SHARED_LIBS=OFF  \
                -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
                -DJPEG_ROOT={$libjpeg_prefix}

                ninja yuv
                cd ..

                mkdir -p {$libyuv_prefix}/lib/
                mkdir -p {$libyuv_prefix}/include/
                cp -f build/libyuv.a {$libyuv_prefix}/lib/
                cp -rf include/* {$libyuv_prefix}/include/


:<<'====EOF===='
                # 移除生成共享库代码
                sed -i.backup1 "33c  " CMakeLists.txt
                sed -i.backup2 "34c  " CMakeLists.txt
                sed -i.backup3 "35c  " CMakeLists.txt
                # 这一行最关键最关键
                sed -i.backup4 "51c  " CMakeLists.txt
                sed -i.backup5 "99c  " CMakeLists.txt

                cmake .. \
                -DCMAKE_INSTALL_PREFIX="{$libyuv_prefix}" \
                -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
                -DJPEG_ROOT={$libjpeg_prefix} \
                -DBUILD_STATIC_LIBS=ON \
                -DBUILD_SHARED_LIBS=OFF  \
                -DCMAKE_BUILD_TYPE="Release"
                cmake --build . --config Release
                cmake --build . --target install --config Release
====EOF====

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
                rm -rf {$libyuv_prefix}/lib/*.so.*
                rm -rf {$libyuv_prefix}/lib/*.so
                rm -rf {$libyuv_prefix}/lib/*.dylib
EOF
            )
            ->withBinPath($libyuv_prefix . '/bin/')
    );
};
