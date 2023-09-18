<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $mesa3d_prefix = MESA3D_PREFIX;

    //  libosmesa.php 与之同样的功能

    $lib = new Library('mesa3d');
    $lib->withHomePage('https://www.mesa3d.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://docs.mesa3d.org/download.html')
        ->withManual('https://docs.mesa3d.org/install.html')

         // mesa3d 在 CPU 上模拟 OpenGL 的 进行静态链接。 但是 并不完全兼容 OpenGL

        ->withUrl('https://archive.mesa3d.org/mesa-23.1.5.tar.xz')


        //补全构建环境缺失软件包
        // bash make-install-deps.sh
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip
            pip3 install mako
EOF
        )
        /** 使用 meson、ninja  构建 start **/
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$mesa3d_prefix} \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true

            meson compile -C build
            meson install -C build

EOF
        )
        /** 使用 meson、ninja  构建 end **/

        ->withBinPath($mesa3d_prefix . '/bin/')
        ->withDependentLibraries('glslang', 'zlib', 'libexpat', 'libdrm')

    ;


    $p->addLibrary($lib);
};
