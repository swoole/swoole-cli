<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('leveldb');
    $lib->withHomePage('https://github.com/google/leveldb.git')
        ->withLicense('https://github.com/google/leveldb/blob/main/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/google/leveldb.git')

        /** 下载依赖库源代码方式二 start **/
        ->withFile('leveldb-latest.tar.gz')
        ->withDownloadScript(
            'leveldb',
            <<<EOF
                git clone -b main  --depth=1 https://github.com/google/leveldb.git
EOF
        )
        /** 下载依赖库源代码方式二 end   **/

        //补全构建环境缺失软件包
        // bash make-install-deps.sh
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip  nasm yasm
            pip3 install meson
EOF
        )
        ->withPreInstallCommand(
            'debian',
            <<<EOF
test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf

mkdir -p /etc/apt/apt.conf.d/

cat > /etc/apt/apt.conf.d/proxy.conf <<'--EOF--'
Acquire::http::Proxy "{$p->getHttpProxy()}";
Acquire::https::Proxy "{$p->getHttpProxy()}";

--EOF--

        apt install -y private package
        test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf
EOF
        )
        ->withPrefix($example_prefix)

        //build_type=dev 才生效
        // 自动清理构建目录  用于调试
        ->withCleanBuildDirectory()
        // 自动清理安装目录  用于调试
        ->withCleanPreInstallDirectory($example_prefix)
        //明确申明 不使用构建缓存 用于调试
        ->withBuildLibraryCached(false)
        //构建过程中添加代理 （特殊库才需要，比如构建 rav1e 库，构建过程中会自动到代码仓库下载）
        ->withBuildLibraryHttpProxy()

        # 构建源码可以使用cmake autoconfig meson 构建
        /** 使用 cmake 构建 start **/
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
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_CXX_STANDARD=14
            -DCMAKE_C_STANDARD=11
            -DCMAKE_C_COMPILER=clang \
            -DCMAKE_CXX_COMPILER=clang++ \
            -DCMAKE_DISABLE_FIND_PACKAGE_libsharpyuv=ON \
            -DCMAKE_C_FLAGS="-D_POSIX_C_SOURCE=200809L" \
            -DOpenSSL_ROOT={$openssl_prefix} \
            -DCMAKE_PREFIX_PATH="{$openssl_prefix}"  # 多个使用分号隔开

            cmake --build . --config Release --target install

EOF
        )
        /** 使用 cmake 构建 end  **/

        /** 使用 meson、ninja  构建 start **/
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            LD_LIBRARY_PATH="{$openssl_prefix}/lib" \
            meson setup  build \
            -Dprefix={$example_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dexamples=disabled \
            -Dc_args=-fmax-errors=10 \
            -Dcpp_args=-DMAGIC=123

            # meson compile -C build

            ninja -C build
            ninja -C build install



EOF
        )
        /** 使用 meson、ninja  构建 end **/

        /** 使用 autoconfig automake  构建 start  **/
        ->withConfigure(
            <<<EOF
            # sh autogen.sh
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
        /** 使用 autoconfig automake  构建 end  **/

        /** 使用 GN 构建 start **/
        ->withBuildScript(
            <<<EOF
        ./update_glslang_sources.py
        gclient sync --gclientfile=standalone.gclient
        gn gen out/Default
EOF
        )
        /** 使用GN 构建 end **/

        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')
        ->withSkipDownload()
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense()

        //依赖其它静态依赖库
        ->withDependentLibraries('zlib', 'openssl', 'depot_tools')
        //默认下不需要，特殊目录才需要配置
        ->withLdflags('-L' . $example_prefix . '/lib/x86_64-linux-gnu/')
        //默认下不需要，特殊目录才需要配置
        ->withPkgConfig($example_prefix . '/lib/x86_64-linux-gnu/pkgconfig')
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$example_prefix}/lib/*.so.*
            rm -rf {$example_prefix}/lib/*.so
            rm -rf {$example_prefix}/lib/*.dylib
EOF
        );

    $p->addLibrary($lib);


    //只有当没有 PKG-CONFIG 配置文件才需要编写这里配置; 例子： src/builder/library/bzip2.php
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $example_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $example_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lexample ');


};
