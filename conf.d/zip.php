<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $liblz4_prefix = LIBLZ4_PREFIX;
    $p->addLibrary(
        (new Library('liblz4'))
            ->withHomePage('http://www.lz4.org')
            ->withLicense('https://github.com/lz4/lz4/blob/dev/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/lz4/lz4/archive/refs/tags/v1.9.4.tar.gz')
            ->withManual('https://github.com/lz4/lz4.git')
            ->withFile('lz4-v1.9.4.tar.gz')
            ->withPkgName('liblz4')
            ->withPrefix($liblz4_prefix)
            ->withCleanBuildDirectory()
            ->withCleanInstallDirectory($liblz4_prefix)
            ->withConfigure(<<<EOF
            cd build/cmake/
            cmake . -DCMAKE_INSTALL_PREFIX={$liblz4_prefix}  -DBUILD_SHARED_LIBS=OFF  -DBUILD_STATIC_LIBS=ON
EOF
            )
    );
    $liblzma_prefix = LIBLZ4_PREFIX;
    $p->addLibrary(
        (new Library('liblzma'))
            ->withHomePage('https://tukaani.org/xz/')
            ->withLicense('https://github.com/tukaani-project/xz/blob/master/COPYING.GPLv3', Library::LICENSE_LGPL)
            ->withManual('https://github.com/tukaani-project/xz.git')
            //->withUrl('https://tukaani.org/xz/xz-5.2.9.tar.gz')
            //->withFile('xz-5.2.9.tar.gz')
            ->withUrl('https://github.com/tukaani-project/xz/releases/download/v5.4.1/xz-5.4.1.tar.gz')
            ->withFile('xz-5.4.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix($liblzma_prefix)
            ->withCleanInstallDirectory($liblzma_prefix)
            ->withConfigure('./configure --prefix=' .$liblzma_prefix . ' --enable-static  --disable-shared --disable-doc')
            ->withPkgName('liblzma')
    );

    $libzstd_prefix = LIBZSTD_PREFIX;
    $p->addLibrary(
        (new Library('libzstd'))
            ->withHomePage('https://github.com/facebook/zstd')
            ->withLicense('https://github.com/facebook/zstd/blob/dev/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://github.com/facebook/zstd/releases/download/v1.5.2/zstd-1.5.2.tar.gz')
            ->withFile('zstd-1.5.2.tar.gz')
            ->withPrefix($libzstd_prefix)
            ->withCleanBuildDirectory()
            ->withCleanInstallDirectory($libzstd_prefix)
            ->withConfigure(
                <<<EOF
            mkdir -p build/cmake/builddir
            cd build/cmake/builddir
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libzstd_prefix} \
            -DZSTD_BUILD_STATIC=ON \
            -DCMAKE_BUILD_TYPE=Release \
            -DZSTD_BUILD_CONTRIB=ON \
            -DZSTD_BUILD_PROGRAMS=ON \
            -DZSTD_BUILD_SHARED=OFF \
            -DZSTD_BUILD_TESTS=OFF \
            -DZSTD_LEGACY_SUPPORT=ON 
EOF
            )
            ->withMakeOptions('lib')
            //->withMakeInstallOptions('install PREFIX=/usr/libzstd/')
            ->withPkgName('libzstd')
            ->depends('liblz4')

    );
    $openssl_prefix = OPENSSL_PREFIX;
    $zip_prefix = ZIP_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('zip'))
            //->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
            ->withUrl('https://libzip.org/download/libzip-1.9.2.tar.gz')
            ->withManual('https://libzip.org')
            ->withPrefix($zip_prefix)
            ->withCleanBuildDirectory()
            ->withCleanInstallDirectory($zip_prefix)
            ->withConfigure(<<<EOF
            cmake -Wno-dev .  \
            -DCMAKE_INSTALL_PREFIX={$zip_prefix} \
            -DCMAKE_BUILD_TYPE=optimized \
            -DBUILD_TOOLS=OFF \
            -DBUILD_EXAMPLES=OFF \
            -DBUILD_DOC=OFF \
            -DLIBZIP_DO_INSTALL=ON \
            -DBUILD_SHARED_LIBS=OFF \
            -DENABLE_GNUTLS=OFF  \
            -DENABLE_MBEDTLS=OFF \
            -DENABLE_OPENSSL=ON \
            -DOPENSSL_USE_STATIC_LIBS=TRUE \
            -DOPENSSL_LIBRARIES={$openssl_prefix}/lib \
            -DOPENSSL_INCLUDE_DIR={$openssl_prefix}/include \
            -DZLIB_LIBRARY={$zlib_prefix}/lib \
            -DZLIB_INCLUDE_DIR={$zlib_prefix}/include \
            -DENABLE_BZIP2=ON \
            -DBZIP2_LIBRARIES={$bzip2_prefix}/lib \
            -DBZIP2_LIBRARY={$bzip2_prefix}/lib \
            -DBZIP2_INCLUDE_DIR={$bzip2_prefix}/include \
            -DBZIP2_NEED_PREFIX=ON \
            -DENABLE_LZMA=ON  \
            -DLIBLZMA_LIBRARY={$liblzma_prefix}/lib \
            -DLIBLZMA_INCLUDE_DIR={$liblzma_prefix}/include \
            -DLIBLZMA_HAS_AUTO_DECODER=ON  \
            -DLIBLZMA_HAS_EASY_ENCODER=ON  \
            -DLIBLZMA_HAS_LZMA_PRESET=ON \
            -DENABLE_ZSTD=ON \
            -DZstd_LIBRARY={$libzstd_prefix}/lib \
            -DZstd_INCLUDE_DIR={$libzstd_prefix}/include
EOF

            )
            ->withMakeOptions('VERBOSE=1')
            ->withPkgName('libzip')
            ->withHomePage('https://libzip.org/')
            ->withLicense('https://libzip.org/license/', Library::LICENSE_BSD)
            ->depends('openssl', 'zlib', 'bzip2','liblzma','libzstd')
    );
    $p->addExtension((new Extension('zip'))->withOptions('--with-zip')->depends('zip'));

    if(0){
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
    }
};
