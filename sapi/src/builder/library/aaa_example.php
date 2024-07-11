<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('aaa_example');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/opencv/opencv.git')

        /*
         *
        //设置现在文件 hash 值验证，hash 值不匹配，下载文件的自动被丢弃
        ->withFileHash('sha1', '32ead1982fed95c52060cd92187a411de3376ac9')
        ->withFileHash('md5','538378de497a830092cd497e2f963b5d')
        ->withFileHash('sha256','5dc841d2dc9f492d57d4550c114f15a03d5ee0275975571ebd82a1fca8604176')


        //设置别名
        ->withAliasName('example')

        //明确申明 使用源地址下载
        ->withDownloadWithOriginURL("https://ftpmirror.gnu.org/gnu/bison/bison-3.8.tar.gz")

        //明确申明 不使用代理
        ->withHttpProxy(false)

        //明确申明 每次都执行下载，不使用已下载的缓存文件
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
            apk add meson
EOF
        )
        ->withPrefix($example_prefix)


        /*

        //明确申明 不使用构建缓存 例子： thirdparty/openssl (每次都解压全新源代码到此目录）
        ->withBuildCached(false)

        //明确申明 不使用库缓存  例子： /usr/local/swoole-cli/zlib (每次构建都需要安装到此目录）
         ->withInstallCached(false)

       */

        # 构建源码可以使用cmake 、 autoconfig 、 meson 构建等


        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$example_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON

        # cmake 查看选项
        # cmake -LH ..
        # 更多配置选项，请查看 CMakeLists.txt 文件
        # 配置选项例子 ；
        # -DCMAKE_INSTALL_LIBDIR={$example_prefix}/lib \
        # -DCMAKE_INSTALL_INCLUDEDIR={$example_prefix}/include \
        # -DCMAKE_CXX_STANDARD=14
        # -DCMAKE_C_STANDARD=11
        # -DCMAKE_C_COMPILER=clang \
        # -DCMAKE_CXX_COMPILER=clang++ \
        # -DCMAKE_DISABLE_FIND_PACKAGE_libsharpyuv=ON \
        # -DCMAKE_C_FLAGS="-D_POSIX_C_SOURCE=200809L" \
        # -DOpenSSL_ROOT={$openssl_prefix} \
        # 查找PKGCONFIG配置目录多个使用分号隔开
        # -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$openssl_prefix}" \
        # 显示构建详情
        # -DCMAKE_VERBOSE_MAKEFILE=ON
        # CMakeLists.txt 设置 set(CMAKE_VERBOSE_MAKEFILEON ON)

        # -DCARES_INCLUDE_DIR={$cares_prefix}/include
        # -DCARES_LIBRARY={$cares_prefix}/lib
        # -DCARES_DIR={$cares_prefix}/
        # -DCARES_ROOT={$cares_prefix}/


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
        -Dprefer_static=true

        # 更多构建选项，请查看 meson_options.txt 文件
        # -Dexamples=disabled
        # -Dc_args=-fmax-errors=10 \
        # -Dcpp_args=-DMAGIC=123


        # meson compile -C build
        # meson install -C build

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

        OPENSSL_CFLAGS=$(pkg-config  --cflags --static openssl)
        OPENSSL_LIBS=$(pkg-config    --libs   --static openssl)

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
        ./configure \
        --prefix={$example_prefix} \
        --enable-shared=no \
        --enable-static=yes

        # 显示构建详情
        # make VERBOSE=1
        # 指定安装目录
        # make DESTDIR=/usr/local/swoole-cli/example
        #

EOF
        )
        /* 使用 autoconfig automake  构建 end  */


        /* 默认不需要此配置


        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$example_prefix}/lib/*.so.*
            rm -rf {$example_prefix}/lib/*.so
            rm -rf {$example_prefix}/lib/*.dylib
EOF
        )

        //没有pkgconfig 配置的库，手动生成 pkgconfig 配置
        ->withScriptAfterInstall(
            <<<EOF
            mkdir -p {$example_prefix}/lib/pkgconfig

            cat << '__example_PKGCONFIG_EOF__' > {$example_prefix}/lib/pkgconfig/libexample.pc
prefix={$example_prefix}/
exec_prefix=\${prefix}/
libdir=\${prefix}/lib
includedir=\${prefix}/include/

Name: example
Description: example
Version: 1.0.0
Requires: zlib
Libs: -L\${libdir} -lexample
Libs.private: -lz -lm
Cflags: -I\${includedir}

__example_PKGCONFIG_EOF__


EOF
        )


        */

        ->withPkgName('libexample')
        ->withBinPath($example_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl')
        /*


        //默认不需要此配置，特殊目录才需要配置
        ->withLdflags('-L' . $example_prefix . '/lib64')

        //默认不需要此配置，特殊目录才需要配置
        ->withPkgConfig($example_prefix . '/lib/ib64/pkgconfig')

        */

    ;

    $p->addLibrary($lib);


    /*

    //只有当没有 pkgconfig  配置文件才需要编写这里配置; 例子： src/builder/library/bzip2.php

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $example_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $example_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lexample ');

    */

    /* 导入需要的变量

    $p->withExportVariable('LIBPQ_CFLAGS', '$(pkg-config  --cflags --static libpq)');
    $p->withExportVariable('LIBPQ_LIBS', '$(pkg-config    --libs   --static libpq)');

     */
};
