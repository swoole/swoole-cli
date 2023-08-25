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
            ->withPreInstallCommand('alpine', <<<EOF
            apk add gn
EOF
            )
            ->withBuildLibraryCached(false)
            ->withBuildScript(
                <<<EOF

             mkdir -p build-dir
             cd build-dir
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libyuv_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_PREFIX_PATH="{$libjpeg_prefix}"


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
    );
};
