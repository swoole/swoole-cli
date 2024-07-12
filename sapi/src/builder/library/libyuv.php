<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $depot_tools_prefix = DEPOT_TOOLS_PREFIX;
    $libyuv_prefix = LIBYUV_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    //文件名称 和 库名称一致
    $lib = new Library('libyuv');
    $lib->withHomePage('https://chromium.googlesource.com/libyuv/libyuv')
        ->withLicense('https://chromium.googlesource.com/libyuv/libyuv/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://chromium.googlesource.com/libyuv/libyuv')
        ->withFile('libyuv-latest.tar.gz')
        //->withAutoUpdateFile()
        ->withDownloadScript(
            'libyuv',
            <<<EOF
            export PATH={$depot_tools_prefix}:\$PATH
            export DEPOT_TOOLS_UPDATE=0
            gclient config --name src https://chromium.googlesource.com/libyuv/libyuv
            gclient -h
            gclient  sync -h
            # gclient  metrics -h
            gclient sync --no-history
            cd ..


EOF
        )
        ->withDownloadScript(
            'libyuv',
            <<<EOF
        git clone -b main --single-branch --depth=1 https://chromium.googlesource.com/libyuv/libyuv

EOF
        )
        ->withPrefix($libyuv_prefix)
        //->withAutoUpdateFile()
        ->withBuildCached(false)
        //->withInstallCached(false)
        ->withBuildScript(
            <<<EOF
        # gn 参数 ：https://gn.googlesource.com/gn
        ls -lha .

        gn gen out/Release "--args=is_debug=false"
        ninja -v -C out/Release
EOF
        )
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$libyuv_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DCMAKE_PREFIX_PATH="{$libjpeg_prefix}" \

        cmake --build . --config Release

        cmake --build . --config Release --target install


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
        ->withDependentLibraries('libjpeg')

    ;
    $p->addLibrary($lib);

};
