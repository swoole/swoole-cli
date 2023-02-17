<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('zip'))
            ->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
            ->withPrefix(ZIP_PREFIX)
            ->withConfigure('
                 cmake -Wno-dev .  \
                -DCMAKE_INSTALL_PREFIX=' . ZIP_PREFIX . ' \
                -DBUILD_TOOLS=OFF \
                -DBUILD_EXAMPLES=OFF \
                -DBUILD_DOC=OFF \
                -DLIBZIP_DO_INSTALL=ON \
                -DBUILD_SHARED_LIBS=OFF \
                -DENABLE_GNUTLS=OFF  \
                -DENABLE_MBEDTLS=OFF \
                -DENABLE_OPENSSL=ON \
                -DOPENSSL_USE_STATIC_LIBS=TRUE \
                -DOPENSSL_LIBRARIES=' . OPENSSL_PREFIX . '/lib \
                -DOPENSSL_INCLUDE_DIR=' . OPENSSL_PREFIX . '/include \
                -DZLIB_LIBRARY=' . ZLIB_PREFIX . '/lib \
                -DZLIB_INCLUDE_DIR=' . ZLIB_PREFIX . '/include \
                -DENABLE_BZIP2=ON \
                -DBZIP2_LIBRARIES=' . BZIP2_PREFIX . '/lib \
                -DBZIP2_LIBRARY=' . BZIP2_PREFIX . '/lib \
                -DBZIP2_INCLUDE_DIR=' . BZIP2_PREFIX . '/include \
                -DBZIP2_NEED_PREFIX=ON \
                -DENABLE_LZMA=OFF  \
                -DENABLE_ZSTD=OFF
            ')
            ->withMakeOptions('VERBOSE=1')
            ->withPkgName('libzip')
            ->withHomePage('https://libzip.org/')
            ->withLicense('https://libzip.org/license/', Library::LICENSE_BSD)
            ->depends('openssl', 'zlib', 'bzip2')
    );
    $p->addExtension((new Extension('zip'))->withOptions('--with-zip'));
};
