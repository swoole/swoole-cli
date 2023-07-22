<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libldns_prefix = LIBLDNS_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('libldns');
    $lib->withHomePage('https://nlnetlabs.nl/projects/ldns/about/')
        ->withLicense('https://github.com/NLnetLabs/ldns/blob/develop/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/NLnetLabs/ldns.git')
        ->withFile('ldns-1.7.1.tar.gz')
        ->withDownloadScript(
            'ldns',
            <<<EOF
            git clone -b release-1.7.1 --depth 1 --progress  https://github.com/NLnetLabs/ldns.git
EOF
        )
        ->withPrefix($libldns_prefix)
        ->withConfigure(
            <<<EOF
            libtoolize -ci
            autoreconf -fi
            ./configure --help
            PACKAGES="openssl libpcap"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) " \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) " \
            ./configure \
            --prefix={$libldns_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-drill \
            --without-pyldns \
            --with-examples \
            --with-ssl={$openssl_prefix} \
            --disable-gost \
            --disable-ecdsa \
            --disable-ed25519 \
            --disable-ed448 \
            --disable-dsa

EOF
        )
        ->withDependentLibraries('openssl', 'libpcap')
        ->withPkgName('ldns')
        ->withBinPath($libldns_prefix . '/bin/');

    $p->addLibrary($lib);
};
