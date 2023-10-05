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
            ->withManual('https://github.com/mongodb/mongo-c-driver/')
            ->withManual('https://mongoc.org/libmongoc/current/installing.html')
            //->withUrl('https://github.com/mongodb/mongo-c-driver/releases/download/1.24.3/mongo-c-driver-1.24.3.tar.gz')
            //->withFile('mongo-c-driver-1.24.4.tar.gz')
            ->withFile('mongo-c-driver-master.tar.gz')
            ->withDownloadScript(
                'mongo-c-driver',
                <<<EOF
                # git clone -b 1.24.4 --depth=1   https://github.com/mongodb/mongo-c-driver.git
                # git clone -b master --depth=1   https://github.com/mongodb/mongo-c-driver.git
                git clone -b fix_static_build --depth=1   https://github.com/mongodb/mongo-c-driver.git
EOF
            )
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
            -DENABLE_TESTS=OFF \
            -DENABLE_EXAMPLES=OFF

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
           rm -rf {$libbson_prefix}/lib/*.so.*
           rm -rf {$libbson_prefix}/lib/*.so
           rm -rf {$libbson_prefix}/lib/*.dylib

EOF
            )

        ->withPkgName('libbson-static-1.0')
        ->withPkgName('libbson-1.0')
    );
};


/*
 * libbson  静态编译补丁
 *  https://github.com/microsoft/vcpkg/pull/10010/files
 */
