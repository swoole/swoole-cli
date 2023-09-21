<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('graphviz');
    $lib->withHomePage('https://www.graphviz.org')
        ->withLicense('https://gitlab.com/graphviz/graphviz/-/blob/main/COPYING', Library::LICENSE_SPEC)
        ->withManual('https://www.graphviz.org')
        ->withFile('graphviz-latest.tar.gz')
        ->withDownloadScript(
            'graphviz',
            <<<EOF
                git clone -b main  --depth=1 https://gitlab.com/graphviz/graphviz.git
EOF
        )

        # 补全构建环境缺失软件包
        // bash make-install-deps.sh
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add  python3

EOF
        )
        ->withPrefix($example_prefix)

         // 当--with-build_type=dev 时 如下2个配置才生效

        // 自动清理构建目录
        ->withCleanBuildDirectory()

        // 自动清理安装目录
        ->withCleanPreInstallDirectory($example_prefix)


        //明确申明 不使用构建缓存 例子： thirdparty/openssl (每次都解压全新源代码到此目录）
        ->withBuildLibraryCached(false)



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

        # 更多配置选项，请查看 CMakeLists.txt 文件
        # 配置选项例子 ；
        # -DCMAKE_CXX_STANDARD=14
        # -DCMAKE_C_STANDARD=11
        # -DCMAKE_C_COMPILER=clang \
        # -DCMAKE_CXX_COMPILER=clang++ \
        # -DCMAKE_DISABLE_FIND_PACKAGE_libsharpyuv=ON \
        # -DCMAKE_C_FLAGS="-D_POSIX_C_SOURCE=200809L" \
        # -DOpenSSL_ROOT={$openssl_prefix} \
        # 查找PKGCONFIG配置目录多个使用分号隔开
        # -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$openssl_prefix}" \


        cmake --build . --config Release

        cmake --build . --config Release --target install


EOF
        )

        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'openssl')

    ;

    $p->addLibrary($lib);

};
