<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $ngtcp2_prefix = NGTCP2_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $p->addLibrary(
        (new Library('ngtcp2'))
            ->withHomePage('https://github.com/ngtcp2/ngtcp2')
            ->withLicense('https://github.com/ngtcp2/ngtcp2/blob/main/COPYING', Library::LICENSE_MIT)
            ->withManual('https://curl.se/docs/http3.html')
            ->withUrl('https://github.com/ngtcp2/ngtcp2/archive/refs/tags/v0.13.1.tar.gz')
            ->withFile('ngtcp2-v0.13.1.tar.gz')
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
            ->withPkgName('libngtcp2_crypto_openssl')
            ->withDependentLibraries('openssl', 'nghttp3')
    );
};
