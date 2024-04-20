<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $grpc_prefix = GRPC_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $cares_prefix = CARES_PREFIX;
    $absl_prefix = ABSL_PREFIX;

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

         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$grpc_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DCMAKE_CXX_STANDARD=17 \
        -DgRPC_ZLIB_PROVIDER=package \
        -DgRPC_CARES_PROVIDER=module \
        -DgRPC_RE2_PROVIDER=module \
        -DgRPC_SSL_PROVIDER=package \
        -DgRPC_PROTOBUF_PROVIDER=module \
        -DgRPC_ABSL_PROVIDER=module \
        -DABSL_CXX_STANDARD=17 \
        -DgRPC_OPENTELEMETRY_PROVIDER=module \
        -DCMAKE_DISABLE_FIND_PACKAGE_libsystemd=ON \
        -DgRPC_BUILD_GRPC_CPP_PLUGIN=OFF \
        -DgRPC_BUILD_GRPC_CSHARP_PLUGIN=OFF \
        -DgRPC_BUILD_GRPC_NODE_PLUGIN=OFF \
        -DgRPC_BUILD_GRPC_OBJECTIVE_C_PLUGIN=OFF \
        -DgRPC_BUILD_GRPC_PHP_PLUGIN=ON \
        -DgRPC_BUILD_GRPC_PYTHON_PLUGIN=OFF \
        -DgRPC_BUILD_GRPC_RUBY_PLUGIN=OFF \
        -DgRPC_BUILD_TESTS=OFF \
        -DCMAKE_PREFIX_PATH="{$zlib_prefix};{$openssl_prefix};{$cares_prefix};{$absl_prefix}"

        cmake --build . --config Release --target install


EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$grpc_prefix}/lib/*.so.*
            rm -rf {$grpc_prefix}/lib/*.so
            rm -rf {$grpc_prefix}/lib/*.dylib
EOF
        )
        ->withPkgName('grpc')
        ->withDependentLibraries(
            'zlib',
            //'cares',
            'openssl',
            //'absl',
            're2'

        );

    $p->addLibrary($lib);

};
