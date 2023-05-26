<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $unixODBC_prefix = UNIX_ODBC_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('unixodbc'))
            ->withHomePage('https://www.unixodbc.org/')
            ->withUrl('https://www.unixodbc.org/unixODBC-2.3.11.tar.gz')
            ->withLicense('https://github.com/lurcher/unixODBC/blob/master/LICENSE', Library::LICENSE_LGPL)
            ->withManual('https://www.unixodbc.org/doc/')
            ->withManual('https://github.com/lurcher/unixODBC.git')
            ->withConfigure(
                <<<EOF
                autoreconf -fi
                ./configure --help
                PACKAGES="readline"
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES )"  \
                LDFLAGS="$(pkg-config --libs-only-L      --static \$PACKAGES )"  \
                LIBS="$(pkg-config --libs-only-l         --static \$PACKAGES )"  \
                CPPFLAGS="\$CPPFLAGS -I{$iconv_prefix}/include"  \
                LDFLAGS="\$LDFLAGS -L{$iconv_prefix}/lib"  \
                LIBS="\$LIBS -liconv"  \
                ./configure \
                --prefix={$unixODBC_prefix} \
                --enable-shared=no \
                --enable-static=yes \
                --enable-iconv \
                --enable-readline \
                --enable-threads
EOF
            )
            ->withBinPath($unixODBC_prefix . '/bin/')

    );
};
