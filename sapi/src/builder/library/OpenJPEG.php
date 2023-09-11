<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openjpeg_prefix = OPENJPEG_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('OpenJPEG');
    $lib->withHomePage('https://www.openjpeg.org/')
        ->withLicense('https://github.com/uclouvain/openjpeg/blob/master/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://github.com/uclouvain/openjpeg/blob/master/INSTALL.md')
        ->withUrl('https://github.com/uclouvain/openjpeg/archive/refs/tags/v2.5.0.tar.gz')
        ->withFile('openjpeg-v2.5.0.tar.gz')

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


            # cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )

        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl')

    ;

    $p->addLibrary($lib);


};
