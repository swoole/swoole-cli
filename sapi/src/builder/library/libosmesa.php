
<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libosmesa_prefix = LIBOSMESA_PREFIX;

    //Off-screen Rendering   //没有opencl 硬件的服务器上运行 VTK

    $lib = new Library('libosmesa');
    $lib->withHomePage('https://www.mesa3d.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://docs.mesa3d.org/osmesa.html')

        // mesa3d 在 CPU 上模拟 OpenGL 的 进行静态链接。 但是 并不完全兼容 OpenGL

        ->withUrl('https://archive.mesa3d.org/mesa-23.1.5.tar.xz')

        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip
            pip3 install mako
EOF
        )

        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$libosmesa_prefix} \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true  \
            -Dosmesa=true \
            -Dgallium-drivers=swrast \
            -Dvulkan-drivers=[]

            meson compile -C build
            meson install -C build

EOF
        )
        /** 使用 meson、ninja  构建 end **/

        ->withBinPath($libosmesa_prefix . '/bin/')
        ->withDependentLibraries('glslang', 'zlib', 'libexpat', 'libdrm', 'libzstd', 'libdrm')

    ;


    $p->addLibrary($lib);
};


/*
 * libomxil-bellagio
 * libtizoia
 * libva
 * tizilheaders
 * libelf
 * libunwind
 * wayland-scanner
 */
