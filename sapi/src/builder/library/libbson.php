<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    # BSON代表“二进制 JSON”
    # https://www.mongodb.com/json-and-bson

    $libbson_prefix = LIBBSON_PREFIX;
    $p->addLibrary(
        (new Library('libbson'))
            ->withHomePage('https://www.mongodb.com/docs/drivers/c/')
            ->withLicense('https://github.com/mongodb/mongo-c-driver/blob/master/COPYING', Library::LICENSE_APACHE2)
            ->withManual('https://mongoc.org/libmongoc/current/tutorial.html')
            ->withUrl('https://github.com/mongodb/mongo-c-driver/releases/download/1.24.3/mongo-c-driver-1.24.3.tar.gz')
            ->withFile('mongo-c-driver-1.24.3.tar.gz')
            ->withPrefix($libbson_prefix)
            ->withBuildLibraryCached(false)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libbson_prefix)
            ->withConfigure(
                <<<EOF

             mkdir -p cmake-build
            cd cmake-build
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libbson_prefix} \
            -DCMAKE_BUILD_TYPE=Release \
            -DBUILD_SHARED_LIBS=OFF \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_POLICY_DEFAULT_CMP0075=NEW \
            -DENABLE_AUTOMATIC_INIT_AND_CLEANUP=OFF \
            -DENABLE_MONGOC=OFF \
            -DCMAKE_DISABLE_FIND_PACKAGE_Python3=ON \
            -DENABLE_TESTS=OFF \
            -DENABLE_EXAMPLES=OFF

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
           rm -rf {$libbson_prefix}/lib/*.so.*
           rm -rf {$libbson_prefix}/lib/*.so
           rm -rf {$libbson_prefix}/lib/*.dylib
           rm -rf {$libbson_prefix}/lib/pkgconfig/libbson-1.0.pc
           cp -f  {$libbson_prefix}/lib/pkgconfig/libbson-static-1.0.pc {$libbson_prefix}/lib/pkgconfig/libbson-1.0.pc
           cp -f  {$libbson_prefix}/lib/pkgconfig/libbson-static-1.0.pc {$libbson_prefix}/lib/pkgconfig/bson-1.0.pc
           cp -f {$libbson_prefix}/lib/libbson-static-1.0.a {$libbson_prefix}/lib/libbson-1.0.a
           cp -f {$libbson_prefix}/lib/libbson-static-1.0.a {$libbson_prefix}/lib/bson-1.0.a


EOF
            )
        ->withPkgName('libbson-static-1.0')
        ->withPkgName('libbson-1.0')
    );
};
