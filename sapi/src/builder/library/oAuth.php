<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $iddawc_prefix = IDDAWC_PREFIX;

    $openssl_prefix = OPENSSL_PREFIX;

    $jansson_prefix = JANSSON_PREFIX;
    $curl_prefix = CURL_PREFIX;
    $libmicrohttpd_prefix = LIBMICROHTTPD_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('oAuth');
    $lib->withHomePage('https://github.com/babelouest/iddawc.git')
        ->withLicense('https://github.com/babelouest/iddawc/blob/master/LICENSE', Library::LICENSE_LGPL)
        ->withManual('https://oauth.net/code/')
        ->withManual('https://oauth.net/code/')
        ->withManual('https://babelouest.github.io/iddawc/')
        ->withFile('iddawc-latest.tar.gz')
        ->withDownloadScript(
            'iddawc',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/babelouest/iddawc.git
EOF
        )
        ->withPrefix($iddawc_prefix)
        // 自动清理安装目录
        ->withCleanPreInstallDirectory($iddawc_prefix)

        //明确申明 不使用构建缓存 例子： thirdparty/openssl (每次都解压全新源代码到此目录）
        ->withBuildCached(false)

        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build
         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$iddawc_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DBUILD_STATIC=ON \
        -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$jansson_prefix};{$curl_prefix};{$libmicrohttpd_prefix};{$zlib_prefix};" \

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



        cmake --build . --config Release

        cmake --build . --config Release --target install


EOF
        )

        ->withPkgName('example')
        ->withBinPath($iddawc_prefix . '/bin/')


        ->withDependentLibraries(
            'zlib',
            'openssl',
            'jansson',
            'curl',
            'libmicrohttpd'
        )


    ;

    $p->addLibrary($lib);

};
