<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $ngtcp2_prefix = NGTCP2_PREFIX;
    $p->addLibrary(
        (new Library('ngtcp2'))
            ->withHomePage('https://github.com/ngtcp2/ngtcp2')
            ->withLicense('https://github.com/ngtcp2/ngtcp2/blob/main/COPYING', Library::LICENSE_MIT)
            ->withManual('https://curl.se/docs/http3.html')
            ->withUrl('https://github.com/ngtcp2/ngtcp2/releases/download/v1.1.0/ngtcp2-1.1.0.tar.gz')
            ->withFile('ngtcp2-1.1.0.tar.gz')
            ->withFileHash('md5', 'e05c501244a2af34b492753763c74e04')
            ->withPrefix($ngtcp2_prefix)
            ->withConfigure(
                <<<EOF
                autoreconf -fi
                ./configure --help

                PACKAGES="openssl libnghttp3 "
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES )"  \
                LDFLAGS="$(pkg-config --libs-only-L      --static \$PACKAGES )"  \
                LIBS="$(pkg-config --libs-only-l         --static \$PACKAGES )"  \
                ./configure \
                --prefix=$ngtcp2_prefix \
                --enable-shared=no \
                --enable-static=yes \
                --enable-lib-only \
                --without-libev \
                --with-openssl  \
                --with-libnghttp3=yes \
                --without-gnutls \
                --without-boringssl \
                --without-picotls \
                --without-wolfssl \
                --without-cunit  \
                --without-jemalloc
EOF
            )
            ->withPkgName('libngtcp2')
            ->withPkgName('libngtcp2_crypto_quictls')
            //->withPkgName('libngtcp2_crypto_openssl') # v1.0 版本 以后变更为 quictls
            ->withDependentLibraries('openssl', 'nghttp3')
    );
};
