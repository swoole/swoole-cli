<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('glad');
    $lib->withHomePage('https://github.com/Dav1dde/glad/wiki/C#quick-start')
        ->withLicense('https://github.com/Dav1dde/glad/blob/glad2/LICENSE', Library::LICENSE_MIT)
        ->withManual('https://github.com/Dav1dde/glad.git')
        ->withManual('https://github.com/Dav1dde/glad/wiki/C#quick-start')
        ->withFile('glad-latest.tar.gz')
        ->withDownloadScript(
            'glad',
            <<<EOF
                git clone -b glad2  --depth=1 https://github.com/Dav1dde/glad.git
EOF
        )


        ->withPrefix($example_prefix)


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
            -DOpenSSL_ROOT={$openssl_prefix}

            cmake --build . --config Release --target install

EOF
        )
        /** 使用 cmake 构建 end  **/

        ->withPkgName('opencv')
        ->withBinPath($example_prefix . '/bin/')
    ;

    $p->addLibrary($lib);

};
