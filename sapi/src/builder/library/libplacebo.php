<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
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

                git clone --recursive -b v5.229.2 --depth=1 https://code.videolan.org/videolan/libplacebo
EOF
        )
        /********************* 下载依赖库源代码方式二 end   *****************************/

        //补全构建环境缺失软件包
        // bash make-install-deps.sh
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip  nasm yasm
            pip3 install meson
EOF
        )
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()  //build_type=dev 才生效  自动清理构建目录  用于调试
        ->withCleanPreInstallDirectory($example_prefix)  //build_type=dev 才生效  自动清理安装目录 用于调试
        ->withBuildLibraryCached(false) //明确申明 不使用构建缓存
        ->withBuildLibraryHttpProxy() //构建过程中添加代理 （特殊库才需要，比如构建 rav1e 库，构建过程中会自动到代码仓库下载）

        /********************************* 使用 meson、ninja  构建 start *************************************/
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$example_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dexamples=disabled

            meson compile -C build

            ninja -C build
            ninja -C build install

EOF
        )
        /********************************* 使用 meson、ninja  构建 end *************************************/

        /********************** 使用 autoconfig automake  构建 start  **********************/
        ->withConfigure(
            <<<EOF
            libtoolize -ci
            autoreconf -fi
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -static" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        /********************** 使用 autoconfig automake  构建 end  **********************/


        ->withPkgName('opencv')
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'openssl') //依赖其它静态依赖库
        ->withLdflags('-L' . $example_prefix . '/lib/x86_64-linux-gnu/') //默认下不需要配，特殊目录才需要配置
        ->withPkgConfig($example_prefix . '/lib/x86_64-linux-gnu/pkgconfig')//默认下不需要配，特殊目录才需要配置
        ->withSkipDownload()
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);


    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $example_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $example_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lopencv ');
};
