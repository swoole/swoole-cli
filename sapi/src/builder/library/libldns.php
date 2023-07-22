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
        ->withBuildLibraryCached(false)
        ->withConfigure(
            <<<EOF
            libtoolize -ci
            autoreconf -fi
            ./configure --help
            ./configure \
            --prefix={$libldns_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-drill \
            --without-pyldns \
            --with-examples \
            --without-ssl
            --with-ssl={$openssl_prefix}

EOF
        )
        ->withDependentLibraries('openssl', 'libpcap')

    ;

    $p->addLibrary($lib);
};
