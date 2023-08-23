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
            ->withUrl('https://github.com/mongodb/mongo-c-driver/releases/download/1.24.3/mongo-c-driver-1.24.3.tar.gz')
            ->withFile('mongo-c-driver-1.24.3.tar.gz')
            ->withPrefix($libmongoc_prefix)
            ->withBuildLibraryCached(false)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libmongoc_prefix)
            ->withBuildScript(
                <<<EOF
             mkdir -p cmake-build
            cd cmake-build
            export BSON_ROOT_DIR={$libbson_prefix}/lib/pkgconfig/
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libmongoc_prefix} \
            -DENABLE_AUTOMATIC_INIT_AND_CLEANUP=OFF \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_POLICY_DEFAULT_CMP0075=NEW \
            -DCMAKE_BUILD_TYPE=Release \
            -DBUILD_SHARED_LIBS=OFF \
            -DBUILD_STATIC_LIBS=ON \
            -DZLIB_ROOT={$zlib_prefix} \
            -DICU_ROOT={$icu_prefix} \
            -DUSE_SYSTEM_LIBBSON=ON \
            -DMONGOC_ENABLE_STATIC_BUILD=ON \
            -DMONGOC_ENABLE_STATIC_INSTALL=ON \
            -DENABLE_STATIC=ON  \
            -DENABLE_SNAPPY=OFF \
            -DENABLE_ZSTD=ON \
            -DENABLE_ZLIB=SYSTEM \
            -DENABLE_SSL=OPENSSL \
            -DENABLE_SASL=OFF \
            -DENABLE_ICU=ON \
            -DENABLE_CLIENT_SIDE_ENCRYPTION=OFF \
            -DENABLE_TESTS=OFF \
            -DENABLE_EXAMPLES=OFF \
            -DCMAKE_PREFIX_PATH="{$libbson_prefix};{$openssl_prefix};{$libzstd_prefix}" \
            -DCMAKE_INCLUDE_PATH="{$libbson_prefix}/include/libbson-1.0"


            cmake --build . --config Release
            cmake --build . --config Release --target install

            #
EOF
            )
            ->withDependentLibraries('openssl', 'readline', 'zlib', 'libzstd', 'icu', 'libbson')
    );
};



/*
 *  libmongoc   静态编译 补丁
 *  https://github.com/microsoft/vcpkg/pull/10010/files
 */
