<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $janus_gateway_prefix = JANUS_GATEWAY_PREFIX;
    $lib = new Library('janus_gateway');
    $lib->withHomePage('https://janus.conf.meetecho.com/')
        ->withLicense('https://github.com/meetecho/janus-gateway/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/meetecho/janus-gateway/archive/refs/tags/v1.1.4.tar.gz')
        ->withManual('https://janus.conf.meetecho.com/')
        ->withFile('janus-gateway-v1.1.4.tar.gz')
        ->withDownloadScript(
            '',
            <<<EOF
            git clone -b v1.1.4 --depth=1  https://github.com/meetecho/janus-gateway.git
EOF
        )

        ->withPrefix($janus_gateway_prefix)
        ->withConfigure(
            <<<EOF
            sh autogen.sh
            PACKAGES="openssl libpq spandsp sofia-sip-ua odbc libjpeg libturbojpeg liblzma libpng sqlite3 zlib libcurl"
            PACKAGES="\$PACKAGES libcares  libbrotlicommon libbrotlidec libbrotlienc"
            PACKAGES="\$PACKAGES libnghttp2 libnghttp3 "
            PACKAGES="\$PACKAGES libngtcp2 libngtcp2_crypto_openssl "

            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) " \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) " \
            ./configure \
            --prefix={$janus_gateway_prefix} \
             --enable-websockets \
             --enable-postprocessing \
             --enable-docs \
             --enable-rest \
             --enable-data-channels
EOF
        )
        ->withDependentLibraries(
            'jansson',
            'libnice',
            'openssl',
            'libsrtp',
            'usrsctp',
            'libmicrohttpd',
            'libwebsockets',
            'curl',
            'sofia_sip',
            'libopus',
            'ogg',
            'zlib',
            'rabbitmq_c',
        )
        ;

    $p->addLibrary($lib);
};
