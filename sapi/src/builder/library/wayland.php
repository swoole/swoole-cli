<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $wayland_prefix = WAYLAND_PREFIX;

    $lib = new Library('wayland');
    $lib->withHomePage('https://wayland.freedesktop.org/')
        ->withLicense('https://gitlab.freedesktop.org/wayland/wayland/-/blob/main/COPYING', Library::LICENSE_MIT)
        ->withManual('https://gitlab.freedesktop.org/wayland/wayland.git')
        ->withFile('wayland-latest.tar.gz')
        ->withDownloadScript(
            'wayland',
            <<<EOF
         git clone -b main  --depth=1 https://gitlab.freedesktop.org/wayland/wayland.git
EOF
        )
        ->withPrefix($wayland_prefix)

        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h

            meson setup  build \
            -Dprefix={$wayland_prefix} \
            -Dlibdir={$wayland_prefix}/lib \
            -Dincludedir={$wayland_prefix}/include \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Ddocumentation=false

            ninja -C build
            ninja -C build install

EOF
        )
        ->withPkgName('wayland-client')
        ->withPkgName('wayland-cursor')
        ->withPkgName('wayland-egl-backend')
        ->withPkgName('wayland-egl')
        ->withPkgName('wayland-scanner')
        ->withPkgName('wayland-server')
        ->withBinPath($wayland_prefix . '/bin/')
        ->withDependentLibraries('libffi', 'libxml2', 'libexpat')



        /*

        //默认不需要此配置
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$wayland_prefix}/lib/*.so.*
            rm -rf {$wayland_prefix}/lib/*.so
            rm -rf {$wayland_prefix}/lib/*.dylib
EOF
        )
        */



    ;

    $p->addLibrary($lib);
};
