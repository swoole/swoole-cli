<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('frrouting');
    $lib->withHomePage('https://frrouting.org/')
        ->withLicense('https://github.com/FRRouting/frr/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withUrl('https://github.com/FRRouting/frr/archive/refs/tags/frr-8.5.2.tar.gz')
        ->withManual('https://frrouting.org/doc/')
        ->withBuildLibraryCached(false)
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)
        ->withConfigure(
            <<<EOF
            sh ./bootstrap.sh
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-static-bin \
            --enable-doc-html \
            --enable-protobuf \
            --with-crypto=openssl \
            --enable-pcre2posix \
            --enable-grpc \
            --enable-sysrepo \
            --enable-confd= \
            --enable-snmp \
            --enable-sharpd
EOF
        )
        ->withSkipDownload()
        ->withPkgName('ssl')
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('libpcap', 'openssl', 'pcre2')
    ;

    $p->addLibrary($lib);
};
