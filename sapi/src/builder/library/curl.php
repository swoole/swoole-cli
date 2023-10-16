<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $file = '';
    $url = 'https://curl.se/download/curl-8.4.0.tar.gz';
    $download_dir_name = '';
    $download_script = '';
    $dependent_libraries = [
        'openssl',
        'cares',
        'zlib',
        'brotli',
        'libzstd',
        'libssh2'
    ];
    $configure_packages = '';
    $configure_options = '';

    if ($php_version_id >= 8010) {
        $dependent_libraries = array_merge($dependent_libraries, ['nghttp2',  'nghttp3', 'ngtcp2']);
        $configure_options = '--with-nghttp2 --with-ngtcp2 --with-nghttp3';
        $configure_packages = ' libnghttp2 libnghttp3 libngtcp2  libngtcp2_crypto_quictls ';
    } else {
        $configure_options = '--without-nghttp2 --without-ngtcp2 --without-nghttp3';
    }


    $curl_prefix = CURL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $cares_prefix = CARES_PREFIX;

    $lib = (new Library('curl'))
        ->withHomePage('https://curl.se/')
        ->withManual('https://curl.se/docs/install.html')
        ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withUrl($url)
        ->withPrefix($curl_prefix)
        ->withConfigure(
            <<<EOF

            ./configure --help

            PACKAGES='zlib openssl libcares libbrotlicommon libbrotlidec libbrotlienc libzstd  '
            PACKAGES="\$PACKAGES  libssh2 {$configure_packages}" # libidn2 libngtcp2_crypto_openssl

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
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
            --enable-threaded-resolver \
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
            --without-libidn2 \
            --with-libssh2 \
            --with-openssl  \
            --with-default-ssl-backend=openssl \
            --without-gnutls \
            --without-mbedtls \
            --without-wolfssl \
            --without-bearssl \
            --without-rustls \
            {$configure_options}

EOF
        )
        ->withPkgName('libcurl')
        ->withBinPath($curl_prefix . '/bin/')
        /*
        ->withDependentLibraries(
            'openssl',
            'cares',
            'zlib',
            'brotli',
            'libzstd',
            'nghttp2',
            'nghttp3',
            'ngtcp2',
            'libssh2'
        ) # 'libidn2',
        */
    ;

    call_user_func_array([$lib, 'withDependentLibraries'], $dependent_libraries);
    $p->addLibrary($lib);
};
