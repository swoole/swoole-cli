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
    $libmnl_prefix = LIBMNL_PREFIX;

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
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add libltdl-static
EOF
        )
        ->withPrefix($unbound_prefix)

        ->withConfigure(
            <<<EOF
           ./configure --help

            # sed -i.backup "s/-ldl/  /g" {$openssl_prefix}/lib/pkgconfig/libcrypto.pc
            # ssed -i.backup "s/-ldl/  /g" {$nettle_prefix}/lib/pkgconfig/hogweed.pc
            # ssed -i.backup "s/-Ldl/  /" {$libevent_prefix}/lib/pkgconfig/libevent_openssl.pc


            PACKAGES='openssl '
            PACKAGES="\$PACKAGES libevent libevent_core libevent_extra libevent_openssl libevent_pthreads"
            PACKAGES="\$PACKAGES expat"
            PACKAGES="\$PACKAGES zlib libxml-2.0 "
            PACKAGES="\$PACKAGES libnghttp2"
            PACKAGES="\$PACKAGES libsodium"
            PACKAGES="\$PACKAGES nettle hogweed gmp"
            PACKAGES="\$PACKAGES hiredis"
            PACKAGES="\$PACKAGES libmnl"

            # PACKAGES="\$PACKAGES libbsd"


            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) " \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) " \
            ./configure \
            --prefix={$unbound_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-libunbound-only \
            --with-pthreads \
            --with-libnghttp2={$nghttp2_prefix} \
            --with-libhiredis={$hiredis_prefix} \
            --with-libexpat={$libexpat_prefix} \
            --with-libevent={$libevent_prefix} \
            --with-ssl={$openssl_prefix} \
            --without-pyunbound \
            --without-pythonmodule \
            --without-dynlibmodule \
            --without-libbsd \
            --enable-dnscrypt \
            --with-libsodium={$libsodium_prefix} \
            --with-libmnl={$libmnl_prefix} \
            --with-nettle={$nettle_prefix} \

            # --enable-fully-static \
            # --enable-dnstap
            # --with-protobuf-c
            # --with-dnstap-socket-path


EOF
        )
        ->withPkgName('libunbound')
        ->withBinPath($unbound_prefix . '/bin/:' . $unbound_prefix . '/sbin/')
        ->withDependentLibraries(
            'openssl',
            'libevent',
            'libsodium',
            'nghttp2',
            'libexpat',
            //'libbsd',
            'nettle',
            'hiredis',
            'libmnl',
            'libxml2',
            'cares',
            'gmp',
            'zlib'
        );

    $p->addLibrary($lib);
};
