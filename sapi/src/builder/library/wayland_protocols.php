<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $wayland_protocols_prefix = WAYLAND_PROTOCOLS_PREFIX;

    $lib = new Library('wayland_protocols');
    $lib->withHomePage('https://gitlab.freedesktop.org/wayland/wayland-protocols.git')
        ->withLicense('https://gitlab.freedesktop.org/wayland/wayland-protocols/-/blob/main/COPYING', Library::LICENSE_MIT)
        ->withManual('https://gitlab.freedesktop.org/wayland/wayland-protocols.git')
        ->withFile('wayland-protocols-latest.tar.gz')
        ->withDownloadScript(
            'wayland-protocols',
            <<<EOF
         git clone -b main  --depth=1 https://gitlab.freedesktop.org/wayland/wayland-protocols.git
EOF
        )
        ->withPrefix($wayland_protocols_prefix)

        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h

            meson setup  build \
            -Dprefix={$wayland_protocols_prefix} \
            -Dlibdir={$wayland_protocols_prefix}/lib \
            -Dincludedir={$wayland_protocols_prefix}/include \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dtests=false

            ninja -C build
            ninja -C build install

EOF
        )
        ->withPkgName('wayland-protocols')
        ->withPkgConfig($wayland_protocols_prefix . '/share/pkgconfig/')
        ->withDependentLibraries('libffi', 'libxml2')

    ;

    $p->addLibrary($lib);
};
