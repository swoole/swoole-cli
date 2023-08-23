<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('example');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/opencv/opencv.git')


        /*

        //明确申明 使用源地址下载
        ->withDownloadWithOriginURL()

        //明确声明，每次都执行下载，不使用已下载的缓存文件
        ->withAutoUpdateFile()

        //明确申明 不使用代理
        ->withHttpProxy(false)

        //明确申明 每次都拉取代码，不使用 pool/lib/opencv-latest.tar.g 文件作为缓存
        ->withAutoUpdateFile()

         //构建过程中添加代理 （特殊库才需要，比如构建 rav1e 库，构建过程中会自动到代码仓库下载）
        ->withBuildLibraryHttpProxy()

        */


        # 下载扩展源代码 二种方式 （任选一种即可）


        /* 下载依赖库源代码方式一 start */
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withFile('opencv-4.7.0.tar.gz')
        /* 下载依赖库源代码方式一 end   */


        /* 下载依赖库源代码方式二 start */
        ->withFile('opencv-latest.tar.gz')
        ->withDownloadScript(
            'opencv',
            <<<EOF
                git clone -b main  --depth=1 https://github.com/opencv/opencv.git
EOF
        )
        /* 下载依赖库源代码方式二 end   */


        # 补全构建环境缺失软件包
        // bash make-install-deps.sh
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip  nasm yasm
            pip3 install meson
EOF
        )
        ->withPrefix($example_prefix)

        /*
         //用于调试
         //当 --with-build_type=dev 时 如下2个配置生效


        // 自动清理构建目录
        ->withCleanBuildDirectory()

        // 自动清理安装目录
        ->withCleanPreInstallDirectory($example_prefix)


        //明确申明 不使用构建缓存
        //例子： thirdparty/openssl (每次都解压全新源代码到此目录）
        ->withBuildLibraryCached(false)

       */


        # 构建源码可以使用cmake 、 autoconfig 、 meson 构建等


        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$example_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON


            # 配置选项例子
            # -DCMAKE_CXX_STANDARD=14
            # -DCMAKE_C_STANDARD=11
            # -DCMAKE_C_COMPILER=clang \
            # -DCMAKE_CXX_COMPILER=clang++ \
            # -DCMAKE_DISABLE_FIND_PACKAGE_libsharpyuv=ON \
            # -DCMAKE_C_FLAGS="-D_POSIX_C_SOURCE=200809L" \
            # -DOpenSSL_ROOT={$openssl_prefix} \
            # 查找PKGCONFIG配置目录多个使用分号隔开
            # -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$openssl_prefix}" \


            cmake --build . --config Release --target install

EOF
        )
        /* 使用 cmake 构建 end  */


        /* 使用 meson、ninja  构建 start */
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

            # -Dc_args=-fmax-errors=10 \
            # -Dcpp_args=-DMAGIC=123


            # meson compile -C build

            ninja -C build
            ninja -C build install
EOF
        )
        /* 使用 meson、ninja  构建 end */


        /* 使用 autoconfig automake  构建 start  */
        ->withConfigure(
            <<<EOF
            # sh autogen.sh

            # libtoolize -ci
            # autoreconf -fi

            ./configure --help

            # LDFLAGS="\$LDFLAGS -static"

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        /* 使用 autoconfig automake  构建 end  */


        /*
        //默认不需要此配置

        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$example_prefix}/lib/*.so.*
            rm -rf {$example_prefix}/lib/*.so
            rm -rf {$example_prefix}/lib/*.dylib
EOF
        )

        */


        ->withPkgName('opencv')
        ->withBinPath($example_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl')


        /*

        //默认不需要此配置，特殊目录才需要配置
        ->withLdflags('-L' . $example_prefix . '/lib/x86_64-linux-gnu/')
        //默认不需要此配置，特殊目录才需要配置
        ->withPkgConfig($example_prefix . '/lib/x86_64-linux-gnu/pkgconfig')

        */
    ;

    $p->addLibrary($lib);


    /*
    //只有当没有 pkgconfig  配置文件才需要编写这里配置; 例子： src/builder/library/bzip2.php

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $example_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $example_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lexample ');

    */
};
