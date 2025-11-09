<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $curl_prefix = CURL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $cares_prefix = CARES_PREFIX;
    # HTTP3 (and QUIC)
    # https://github.com/curl/curl/blob/master/docs/HTTP3.md#openssl-version
    $p->addLibrary(
        (new Library('curl'))
            ->withHomePage('https://curl.se/')
            ->withManual('https://curl.se/docs/install.html')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withUrl('https://github.com/curl/curl/releases/download/curl-8_16_0/curl-8.16.0.tar.gz')
            ->withFileHash('md5', '3db9de72cc8f04166fa02d3173ac78bb')
            ->withPrefix($curl_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help

            PACKAGES='zlib openssl libcares libbrotlicommon libbrotlidec libbrotlienc libzstd  '
            PACKAGES="\$PACKAGES  libssh2 libnghttp2 libnghttp3  libidn2 libpsl " # libngtcp2

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$openssl_prefix}/include/openssl/" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure --prefix={$curl_prefix}  \
            --enable-static \
            --disable-shared \
            --without-librtmp \
            --disable-ldap \
            --disable-rtsp \
            --enable-http \
            --enable-alt-svc \
            --enable-hsts \
            --enable-http-auth \
            --enable-mime \
            --enable-cookies \
            --enable-doh \
            --enable-ipv6 \
            --enable-proxy  \
            --enable-websockets \
            --enable-get-easy-options \
            --enable-file \
            --enable-mqtt \
            --enable-unix-sockets  \
            --enable-progress-meter \
            --enable-optimize \
            --with-zlib={$zlib_prefix} \
            --enable-ares={$cares_prefix} \
            --with-nghttp2 \
            --with-nghttp3 \
            --with-libidn2 \
            --with-libssh2 \
            --with-openssl  \
            --with-default-ssl-backend=openssl \
            --with-openssl-quic \
            --without-gnutls \
            --without-mbedtls \
            --without-wolfssl \
            --without-libressl \
            --without-rustls

EOF
            )
            ->withPkgName('libcurl')
            ->withBinPath($curl_prefix . '/bin/')
            ->withDependentLibraries(
                'openssl',
                'cares',
                'zlib',
                'brotli',
                'libzstd',
                'nghttp2',
                'nghttp3',
                //'ngtcp2',
                'libssh2',
                'libidn2',
                'libpsl'
            )
    );
};
