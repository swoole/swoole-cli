<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $options = 'cmake -Wno-dev .  \
                -DCMAKE_INSTALL_PREFIX=' . ZIP_PREFIX . ' \
                -DBUILD_TOOLS=OFF \
                -DBUILD_EXAMPLES=OFF \
                -DBUILD_DOC=OFF \
                -DLIBZIP_DO_INSTALL=ON \
                -DBUILD_SHARED_LIBS=OFF \
                -DENABLE_GNUTLS=OFF  \
                -DENABLE_MBEDTLS=OFF \
                -DOPENSSL_USE_STATIC_LIBS=TRUE \\' . PHP_EOL;
    if ($p->getInputOption('zip-openssl')) {
        $options .= '-DENABLE_OPENSSL=ON \
                -DOPENSSL_LIBRARIES=' . OPENSSL_PREFIX . '/lib \
                -DOPENSSL_INCLUDE_DIR=' . OPENSSL_PREFIX . '/include \\' . PHP_EOL;
    } else {
        $options .= '-DENABLE_OPENSSL=OFF \\' . PHP_EOL;
    }
    if ($p->getInputOption('zip-zlib', 'yes') == 'yes') {
        $options .= '-DZLIB_LIBRARY=' . ZLIB_PREFIX . '/lib \
                -DZLIB_INCLUDE_DIR=' . ZLIB_PREFIX . '/include \\' . PHP_EOL;
    }
    if ($p->getInputOption('zip-bz2', 'yes') == 'yes') {
        $options .= '-DENABLE_BZIP2=ON \
                -DBZIP2_LIBRARIES=' . BZIP2_PREFIX . '/lib \
                -DBZIP2_LIBRARY=' . BZIP2_PREFIX . '/lib \
                -DBZIP2_NEED_PREFIX=ON \
                -DBZIP2_INCLUDE_DIR=' . BZIP2_PREFIX . '/include \\' . PHP_EOL;
    } else {
        $options .= '-DENABLE_BZIP2=OFF \\' . PHP_EOL;
    }
    $options .= '-DENABLE_LZMA=OFF  \
                -DENABLE_ZSTD=OFF';

    $zip_library = (new Library('zip'))
        ->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
        ->withPrefix(ZIP_PREFIX)
        ->withConfigure($options)
        ->withMakeOptions('VERBOSE=1')
        ->withPkgName('libzip')
        ->withHomePage('https://libzip.org/')
        ->withLicense('https://libzip.org/license/', Library::LICENSE_BSD);

    if ($p->getInputOption('zip-openssl')) {
        $zip_library->depends('openssl');
    }
    if ($p->getInputOption('zip-zlib')) {
        $zip_library->depends('zlib');
    }
    if ($p->getInputOption('zip-bz2')) {
        $zip_library->depends('bzip2');
    }
    $p->addLibrary($zip_library);
    $p->addExtension((new Extension('zip'))->withOptions('--with-zip'));
};
