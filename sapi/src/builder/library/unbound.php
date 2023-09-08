<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $unbound_prefix = UNBOUND_PREFIX;
    $libevent_prefix = LIBEVENT_PREFIX;
    $libsodium_prefix = LIBSODIUM_PREFIX;
    $nghttp2_prefix = NGHTTP2_PREFIX;
    $libexpat_prefix = LIBEXPAT_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $nettle_prefix = NETTLE_PREFIX;
    $hiredis_prefix = HIREDIS_PREFIX;


    $lib = new Library('unbound');
    $lib->withHomePage('http://www.unbound.net/')
        ->withHomePage('https://nlnetlabs.nl/projects/unbound/about/')
        ->withLicense('https://github.com/NLnetLabs/unbound/blob/master/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/NLnetLabs/unbound.git')
        ->withFile('unbound-latest.tar.gz')
        ->withDownloadScript(
            'unbound',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/NLnetLabs/unbound.git
EOF
        )
        ->withPrefix($unbound_prefix)
        /* 使用 autoconfig automake  构建 start  */
        ->withConfigure(
            <<<EOF
           ./configure --help
            set -ex
            PACKAGES='openssl '
            PACKAGES="\$PACKAGES libevent libevent_core libevent_extra libevent_openssl libevent_pthreads"
            PACKAGES="\$PACKAGES expat"
            PACKAGES="\$PACKAGES libnghttp2"
            PACKAGES="\$PACKAGES libsodium"
            # PACKAGES="\$PACKAGES libbsd"
            PACKAGES="\$PACKAGES nettle"
            PACKAGES="\$PACKAGES hiredis"

            # CFLAGS="-std=c99 -D__EXTENSIONS__ -D_BSD_SOURCE -D_DEFAULT_SOURCE -D_POSIX_C_SOURCE=200112 -D_XOPEN_SOURCE=600 -D_XOPEN_SOURCE_EXTENDED=1 -D_ALL_SOURCE " \

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$unbound_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-fully-static \
            --with-libunbound-only \
            --with-libnghttp2={$nghttp2_prefix} \
            --with-libhiredis={$hiredis_prefix} \
            --with-libexpat={$libexpat_prefix} \
            --with-libevent={$libevent_prefix} \
            --with-ssl={$openssl_prefix} \
            --with-nettle={$nettle_prefix} \
            --without-pyunbound \
            --without-pythonmodule \
            --without-dynlibmodule

            # --with-libbsd \
            # --enable-dnscrypt \
            # --with-libsodium={$libsodium_prefix} \
            # --enable-dnstap
            # --with-protobuf-c
            # --with-dnstap-socket-path


EOF
        )
        ->withDependentLibraries(
            'openssl',
            'libevent',
            'libsodium',
            'nghttp2',
            'libexpat',
            'libbsd',
            'nettle',
            'hiredis'
        );

    $p->addLibrary($lib);

};
