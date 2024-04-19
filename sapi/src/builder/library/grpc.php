<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $grpc_prefix = EXAMPLE_PREFIX;
    $grpc_prefix = GRPC_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix =ZLIB_PREFIX;
    $cares_prefix = CARES_PREFIX;
    $absl_prefix =ABSL_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('grpc');
    $lib->withLicense('https://github.com/grpc/grpc/blob/master/src/php/ext/grpc/LICENSE', Library::LICENSE_APACHE2)
        ->withHomePage('grpc.io')
        ->withManual('https://github.com/grpc/grpc/tree/master/src/php/ext')
        ->withFile('grpc-latest.tar.gz')
        ->withDownloadScript(
            'grpc', # 待打包目录名称
            <<<EOF
            git clone -b master --depth=1 --recursive  https://github.com/grpc/grpc.git

EOF
        )
        ->withPrefix($grpc_prefix)

        ->withBuildScript(
            <<<EOF

         # EXTRA_DEFINES=GRPC_POSIX_FORK_ALLOW_PTHREAD_ATFORK make

         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$grpc_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DCMAKE_PREFIX_PATH="{$zlib_prefix};{$cares_prefix};{$openssl_prefix};{$absl_prefix}"


        cmake --build . --config Release --target install


EOF
        )
        ->withDependentLibraries('zlib','cares','openssl','abseil_cpp')
    ;

    $p->addLibrary($lib);

};
