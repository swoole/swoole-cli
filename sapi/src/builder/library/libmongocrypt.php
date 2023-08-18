<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmongocrypt_prefix = LIBMONGOCRYPT_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $lib = new Library('libmongocrypt');
    $lib->withHomePage('https://github.com/mongodb/libmongocrypt.git')
        ->withLicense('https://github.com/mongodb/libmongocrypt/blob/master/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/mongodb/libmongocrypt.git')

        ->withFile('libmongocrypt-latest.tar.gz')
        ->withDownloadScript(
            'libmongocrypt',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/mongodb/libmongocrypt.git
EOF
        )
        ->withPrefix($libmongocrypt_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libmongocrypt_prefix)
        ->withBuildLibraryCached(false)
        //->withAutoUpdateFile()
            ->withBuildLibraryHttpProxy()
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libmongocrypt_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_TESTING=OFF \
            -DMONGOCRYPT_CRYPTO=OpenSSL \
            -DUSE_SHARED_LIBBSON=OFF \
            -DENABLE_BUILD_FOR_PPA=OFF \
            -DENABLE_STATIC=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_libsasl2=OFF \
            -DZLIB_ROOT={$zlib_prefix}


            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('example')
        ->withBinPath($libmongocrypt_prefix . '/bin/')
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$libmongocrypt_prefix}/lib/*.so.*
            rm -rf {$libmongocrypt_prefix}/lib/*.so
            rm -rf {$libmongocrypt_prefix}/lib/*.dylib
EOF
        )
        ->withDependentLibraries('openssl', 'libbson','zlib')
    ;

    $p->addLibrary($lib);
};
