<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openssl_prefix = OPENSSL_PREFIX;
    $libzip_prefix = ZIP_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('libzip'))
            ->withHomePage('https://libzip.org/')
            ->withLicense('https://libzip.org/license/', Library::LICENSE_BSD)
            ->withUrl('https://libzip.org/download/libzip-1.9.2.tar.gz')
            ->withManual('https://libzip.org')
            ->withPrefix($libzip_prefix)
            ->withConfigure(
                <<<EOF
            mkdir -p build
            cd build
            # cmake -LH ..
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libzip_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release \
            -DBUILD_SHARED_LIBS=OFF \
            -DBUILD_TOOLS=ON \
            -DBUILD_EXAMPLES=OFF \
            -DBUILD_DOC=OFF \
            -DLIBZIP_DO_INSTALL=ON \
            -DENABLE_GNUTLS=OFF  \
            -DENABLE_MBEDTLS=OFF \
            -DENABLE_OPENSSL=ON \
            -DOPENSSL_USE_STATIC_LIBS=TRUE \
            -DENABLE_BZIP2=ON \
            -DENABLE_COMMONCRYPTO=OFF \
            -DENABLE_LZMA=ON \
            -DENABLE_ZSTD=ON \
            -DOpenSSL_ROOT={$openssl_prefix} \
            -DZLIB_ROOT={$zlib_prefix} \
            -DBZip2_ROOT={$bzip2_prefix} \
            -DLibLZMA_ROOT={$liblzma_prefix} \
            -DZstd_ROOT={$libzstd_prefix}

EOF
            )
            ->withMakeOptions('VERBOSE=1')
            ->withPkgName('libzip')
            ->withBinPath($libzip_prefix . '/bin/')
            ->withDependentLibraries('openssl', 'zlib', 'bzip2', 'liblzma', 'libzstd')
    );
};
