<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libplacebo_prefix = LIBPLACEBO_PREFIX;
    $lib = new Library('libplacebo');
    $lib->withHomePage(' https://code.videolan.org/videolan/libplacebo')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://code.videolan.org/videolan/libplacebo')

        /********************* 下载依赖库源代码方式二 start *****************************/
        ->withAutoUpdateFile() # 明确申明 每次都拉取代码，不使用 pool/lib/opencv-latest.tar.g 文件作为缓存
        ->withFile('libplacebo-v5.229.2.tar.gz')
        ->withDownloadScript(
            'libplacebo',
            <<<EOF
                git clone --recursive -b v5.229.2 --depth=1 https://code.videolan.org/videolan/libplacebo.git

EOF
        )

        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip
            pip3 install meson
EOF
        )
        ->withPrefix($libplacebo_prefix)
        /********************************* 使用 meson、ninja  构建 start *************************************/
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$libplacebo_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dvulkan=enabled \
            -Dvk-proc-addr=disabled \
            -Dvulkan-registry={$libplacebo_prefix}/share/vulkan/registry/vk.xml \
            -Dshaderc=enabled \
            -Dglslang=disabled \
            -Ddemos=false \
            -Dtests=false \
            -Dbench=false \
            -Dfuzz=false

            ninja -C build
            ninja -C build install

EOF
        )

        ->withBinPath($libplacebo_prefix . '/bin/')
        ->withDependentLibraries('libunwind', 'execinfo', 'shaderc', 'vulkan')
    ;


    $p->addLibrary($lib);
};
