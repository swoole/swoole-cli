<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libpcap_prefix = LIBPCAP_PREFIX;
    $lib = new Library('libpcap');
    $lib->withHomePage('https://www.tcpdump.org/')
        ->withLicense('https://github.com/NLnetLabs/ldns/blob/develop/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/the-tcpdump-group/libpcap')
        ->withManual('https://www.tcpdump.org/')
        ->withUrl('https://www.tcpdump.org/release/libpcap-1.10.4.tar.gz')
        ->withPrefix($libpcap_prefix)
        ->withConfigure(
            <<<EOF
            sh /autogen.sh
            ./configure --help
            PACKAGES="openssl"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) " \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) " \
            ./configure \
            --prefix={$libpcap_prefix} \
            --enable-shared=no \
            --enable-netmap \
            --enable-rdma

EOF
        )
        ->withDependentLibraries('openssl')

    ;

    $p->addLibrary($lib);
};
