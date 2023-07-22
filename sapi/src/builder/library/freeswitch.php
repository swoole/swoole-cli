<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $freeswitch_prefix = FREESWITCH_PREFIX;
    $odbc_prefix = UNIX_ODBC_PREFIX;
    $lib = new Library('freeswitch');
    $lib->withHomePage('https://github.com/signalwire/freeswitch.git')
        ->withLicense('https://github.com/signalwire/freeswitch/blob/master/LICENSE', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/signalwire/freeswitch/archive/refs/tags/v1.10.9.tar.gz')
        ->withManual('https://freeswitch.com/#getting-started')
        ->withFile('freeswitch-v1.10.9.tar.gz')
        ->withDownloadScript(
            'freeswitch',
            <<<EOF
                git clone -b v1.10.9  --depth=1 https://github.com/signalwire/freeswitch.git
EOF
        )
        ->withPrefix($freeswitch_prefix)
        ->withBuildLibraryCached(false)
        ->withPreInstallCommand(
            <<<EOF
        apt install libtool  libtool-bin
EOF
        )
        ->withBuildScript(
            <<<EOF
            ./bootstrap.sh
            ./configure --help

            PACKAGES="openssl libpq"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES )" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES )" \
            CFLAGS="-O3 -std=c11 -g " \
            ./configure \
            --prefix={$freeswitch_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-optimization \
            --with-openssl \
            --with-python3 \
            --with-odbc={$odbc_prefix} \
            --enable-systemd=no \


          # make install
EOF
        )
        ->withDependentLibraries('openssl', 'pgsql', 'spandsp')
    ;

    $p->addLibrary($lib);
};
