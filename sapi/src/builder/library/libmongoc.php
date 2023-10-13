<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmongoc_prefix = LIBMONGOC_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $libzip_prefix = ZIP_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $icu_prefix = ICU_PREFIX;
    $libbson_prefix = LIBBSON_PREFIX;
    $p->addLibrary(
        (new Library('libmongoc'))
            ->withHomePage('https://www.mongodb.com/docs/drivers/c/')
            ->withLicense('https://github.com/mongodb/mongo-c-driver/blob/master/COPYING', Library::LICENSE_APACHE2)
            ->withManual('https://mongoc.org/libmongoc/current/tutorial.html')
            ->withManual('https://mongoc.org/libmongoc/current/installing.html')
            //->withUrl('https://github.com/mongodb/mongo-c-driver/releases/download/1.24.3/mongo-c-driver-1.24.3.tar.gz')
            //->withFile('mongo-c-driver-1.24.4.tar.gz')
            //->withFile('mongo-c-driver-1.24.4.tar.gz')
            ->withFile('mongo-c-driver-master.tar.gz')
            ->withDownloadScript(
                'mongo-c-driver',
                <<<EOF
                # git clone -b 1.24.4 --depth=1   https://github.com/mongodb/mongo-c-driver.git
                # git clone -b master --depth=1   https://github.com/mongodb/mongo-c-driver.git
                git clone -b fix_static_build --depth=1   https://github.com/jingjingxyk/mongo-c-driver.git
EOF
            )
            ->withPrefix($libmongoc_prefix)
            //->withAutoUpdateFile()
            //->withBuildCached(false)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libmongoc_prefix)
            ->withBuildScript(
                <<<EOF
             mkdir -p cmake-build
            cd cmake-build

            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libmongoc_prefix} \
            -DCMAKE_C_STANDARD=11 \
            -DCMAKE_BUILD_TYPE=Release \
            -DBUILD_SHARED_LIBS=OFF \
            -DBUILD_STATIC_LIBS=ON \
            -DENABLE_MONGOC=ON \
            -DENABLE_STATIC=ON \
            -DENABLE_SHARED=OFF \
            -DENABLE_TESTS=OFF \
            -DENABLE_EXAMPLES=OFF \
            -DENABLE_SRV=ON \
            -DENABLE_SNAPPY=OFF \
            -DENABLE_ZLIB=SYSTEM \
            -DZLIB_ROOT={$zlib_prefix} \
            -DENABLE_ZSTD=ON \
            -DENABLE_SSL=OPENSSL \
            -DENABLE_SASL=OFF \
            -DENABLE_CLIENT_SIDE_ENCRYPTION=OFF \
            -DENABLE_MONGODB_AWS_AUTH=OFF \
            -DENABLE_CRYPTO_SYSTEM_PROFILE=OFF \
            -DENABLE_AUTOMATIC_INIT_AND_CLEANUP=OFF \
            -DENABLE_EXTRA_ALIGNMENT=OFF \
            -DUSE_SYSTEM_LIBBSON=OFF \
            -DMONGOC_ENABLE_STATIC_BUILD=ON \
            -DMONGOC_ENABLE_STATIC_INSTALL=ON \
            -DENABLE_ICU=ON \
            -DICU_ROOT=ON \
            -DICU_ROOT={$icu_prefix} \
            -DCMAKE_PREFIX_PATH="{$openssl_prefix}"


            {
                cmake --build . --config Release --target install
            } || {
                echo $?
            }

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            sed -i.backup "s/libmongoc-1\.0/libmongoc-static-1\.0/"  {$libmongoc_prefix}/lib/pkgconfig/libmongoc-ssl-1.0.pc

EOF
            )
            ->withDependentLibraries('openssl', 'readline', 'zlib', 'libzstd', 'icu') //'libbson'
            ->withPkgName('libbson-static-1.0')
            ->withPkgName('libmongoc-ssl-1.0')
            ->withPkgName('libmongoc-static-1.0')
            ->withBinPath($libmongoc_prefix . '/bin/')
    );
};



/*
 *  libmongoc   静态编译 补丁
 *  https://github.com/microsoft/vcpkg/pull/10010/files
 */
