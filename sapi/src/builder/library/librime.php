<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = LIBRIME_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $glog_prefix = GLOG_PREFIX;
    $libyaml_cpp_prefix = LIBYAML_CPP_PREFIX;
    $leveldb_prefix = LEVELDB_PREFIX;
    $lib = new Library('librime');
    $lib->withHomePage('https://rime.im/')
        ->withLicense('https://github.com/rime/librime/blob/master/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/rime/librime/blob/master/CMakeLists.txt')
        ->withManual('https://github.com/rime/librime.git')
        ->withFile('librime-latest.tar.gz')
        ->withDownloadScript(
            'librime',
            <<<EOF
                git clone -b master  --depth=1 --recursive https://github.com/rime/librime.git
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
            -DBUILD_STATIC=ON \
            -DBUILD_DATA=ON \
            -DBUILD_TEST=OFF \
            -DBoost_ROOT=/usr/ \
            -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$glog_prefix};{$libyaml_cpp_prefix};{$leveldb_prefix}"

            cmake --build . --config Release --target install

EOF
        )
        /** 使用 cmake 构建 end  **/

        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')

        ->withDependentLibraries('glog', 'leveldb', 'libopencc', 'libyaml_cpp','libmarisa')

        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$example_prefix}/lib/*.so.*
            rm -rf {$example_prefix}/lib/*.so
            rm -rf {$example_prefix}/lib/*.dylib
EOF
        );

    $p->addLibrary($lib);
};

/*
 * GB18030-2022新增汉字 中文编码字符集
 * https://openstd.samr.gov.cn/bzgk/gb/newGbInfo?hcno=A1931A578FE14957104988029B0833D3
 */
