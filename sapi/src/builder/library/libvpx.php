<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libvpx_prefix = LIBVPX_PREFIX;
    $lib = new Library('libvpx');
    $lib->withHomePage('https://chromium.googlesource.com/webm/libvpx')
        ->withLicense('https://chromium.googlesource.com/webm/libvpx/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://chromium.googlesource.com/webm/libvpx/+/refs/heads/main')
        ->withFile('libvpx-v1.13.0.tar.gz')
        ->withDownloadScript(
            'libvpx',
            <<<EOF
            git clone -b v1.13.0  --depth=1  https://chromium.googlesource.com/webm/libvpx
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add diffutils
EOF
        )
        ->withPreInstallCommand(
            'debian',
            <<<EOF
        apt install -y diffutils
EOF
        )
        ->withPreInstallCommand(
            'ubuntu',
            <<<EOF
        apt install -y diffutils
EOF
        )
        ->withPrefix($libvpx_prefix)
        ->withConfigure(
            <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libvpx_prefix} \
            --disable-shared \
            --enable-static \
            --enable-vp8 \
            --enable-vp9 \
            --enable-pic \
            --disable-examples \
            --disable-tools \
            --disable-docs \
            --disable-unit-tests \
            --enable-vp9-highbitdepth

EOF
        )
        ->withPkgName('vpx')
        ->withBinPath($libvpx_prefix . '/bin/');

    $p->addLibrary($lib);
};
