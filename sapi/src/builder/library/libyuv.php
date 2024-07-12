<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libyuv_prefix = EXAMPLE_PREFIX;
    $libyuv_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;
    $cares_prefix = CARES_PREFIX;
    //文件名称 和 库名称一致
    $lib = new Library('libyuv');
    $lib->withHomePage('https://chromium.googlesource.com/libyuv/libyuv')
        ->withLicense('https://chromium.googlesource.com/libyuv/libyuv/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://chromium.googlesource.com/libyuv/libyuv')
        ->withFile('libyuv-latest.tar.gz')
        ->withDownloadScript(
            'libyuv',
            <<<EOF
               git clone -b main --single-branch --depth=1 https://chromium.googlesource.com/libyuv/libyuv
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip  nasm yasm meson
EOF
        )
        ->withPrefix($libyuv_prefix)

        ->withBuildCached(false)

         ->withInstallCached(false)

        ->withBuildScript(
            <<<EOF
        gn gen out/Release "--args=is_debug=false"
        ninja -v -C out/Release
EOF
        )

        ->withPkgName('libexample')
        ->withBinPath($libyuv_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'openssl')

    ;
    $p->addLibrary($lib);

};
