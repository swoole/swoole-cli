<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libssh2_prefix = LIBSSH2_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $p->addLibrary(
        (new Library('libssh2'))
            ->withHomePage('https://www.libssh2.org/')
            ->withLicense('https://www.libssh2.org/license.html', Library::LICENSE_SPEC)
            ->withManual('https://github.com/libssh2/libssh2.git')
            ->withManual('https://github.com/libssh2/libssh2/blob/master/docs/INSTALL_CMAKE.md')
            ->withUrl('https://www.libssh2.org/download/libssh2-1.10.0.tar.gz')
            ->withPrefix($libssh2_prefix)
            ->withBuildScript(
                <<<EOF
              mkdir -p build
              cd build
              cmake .. \
              -DCMAKE_INSTALL_PREFIX={$libssh2_prefix} \
              -DCMAKE_BUILD_TYPE=Release  \
              -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
              -DBUILD_STATIC_LIBS=ON \
              -DBUILD_SHARED_LIBS=OFF \
              -DENABLE_ZLIB_COMPRESSION=ON  \
              -DZLIB_ROOT={$zlib_prefix} \
              -DCLEAR_MEMORY=ON  \
              -DENABLE_GEX_NEW=ON  \
              -DENABLE_CRYPT_NONE=OFF  \
              -DOpenSSL_ROOT={$openssl_prefix} \
              -DCRYPTO_BACKEND=OpenSSL \
              -DBUILD_TESTING=OFF \
              -DBUILD_EXAMPLES=OFF

              cmake --build . --target install
EOF
            )
            ->withPkgName('libssh2')
            ->withDependentLibraries('zlib', 'openssl')
    );
};
