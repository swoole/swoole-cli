<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libudev_prefix = LIBUDEV_PREFIX;

    $lib = new Library('libudev');
    $lib->withHomePage('https://github.com/systemd/systemd.git')
        ->withLicense('https://github.com/systemd/systemd/blob/main/LICENSE.GPL2', Library::LICENSE_LGPL)
        //->withUrl('https://github.com/systemd/systemd/archive/refs/tags/v254-rc1.tar.gz')
        ->withManual('https://github.com/systemd/systemd.git')
        ->withFile('libudev-v254.tar.gz')
        ->withDownloadScript(
            'systemd',
            <<<EOF
        git clone -b v254 --depth=1 https://github.com/systemd/systemd.git
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add gperf libcap-dev mount
EOF
        )
        ->withPrefix($libudev_prefix)
        ->withBuildScript(
            <<<EOF

        meson setup  build \
        -Dprefix={$libudev_prefix} \
        -Dlibdir={$libudev_prefix}/lib \
        -Dincludedir={$libudev_prefix}/include \
        -Dbackend=ninja \
        -Dbuildtype=release \
        -Ddefault_library=static \
        -Db_staticpic=true \
        -Db_pie=true \
        -Dprefer_static=true \
        -Dmode=release \
        -Dstatic-libudev=true \
        -Dstatic-libsystemd=true \
        -Dlink-udev-shared=false  \
        -Dlink-systemctl-shared=false  \
        -Dlink-networkd-shared=false  \
        -Dlink-timesyncd-shared=false  \
        -Dlink-journalctl-shared=false  \
        -Dlink-boot-shared=false  \
        -Dlink-portabled-shared=false

        ninja -C build
        ninja -C build install
EOF
        )
        ->withDependentLibraries('libcap_ng', 'openssl')
    ;

    $p->addLibrary($lib);
};
