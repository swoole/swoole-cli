<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openexr_prefix = EXAMPLE_PREFIX;
    $openexr_prefix = OPENEXR_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;


    $lib = new Library('OpenEXR');
    $lib->withHomePage('https://openexr.com/en/latest/')
        ->withLicense('https://openexr.com/en/latest/license.html#license', Library::LICENSE_BSD)
        ->withManual('https://openexr.com/en/latest/install.html#install')
        ->withFile('openexr-latest.tar.gz')
        ->withDownloadScript(
            'openexr',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/AcademySoftwareFoundation/openexr.git
EOF
        )



        # 补全构建环境缺失软件包
        // bash make-install-deps.sh
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip  nasm yasm
            pip3 install meson
EOF
        )
        ->withPrefix($openexr_prefix)

        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$openexr_prefix} \
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
        ->withBinPath($openexr_prefix . '/bin/')

        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl')

    ;

    $p->addLibrary($lib);

};
