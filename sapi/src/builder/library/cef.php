<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('example');
    $lib->withHomePage('https://bitbucket.org/chromiumembedded/cef/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/chromiumembedded/cef.git')
        /** 下载依赖库源代码方式二 start **/
        ->withFile('cef-latest.tar.gz')
        ->withDownloadScript(
            'cef',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/chromiumembedded/cef.git
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


        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')


        //依赖其它静态依赖库
        ->withDependentLibraries('zlib', 'openssl', 'depot_tools')
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
