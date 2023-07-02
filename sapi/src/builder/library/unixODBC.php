<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $odbc_prefix = UNIX_ODBC_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('unixODBC'))
            ->withHomePage('https://github.com/lurcher/unixODBC')
            ->withLicense('https://github.com/lurcher/unixODBC/blob/master/LICENSE', Library::LICENSE_LGPL)
            ->withUrl('https://github.com/lurcher/unixODBC/releases/download/2.3.11/unixODBC-2.3.11.tar.gz')
            ->withPrefix($odbc_prefix)
            ->withconfigure(
                <<<EOF
            autoreconf -ivf
            ./configure --help

            PACKAGES_NAMES="readline"
            CPPFLAGS="\$(pkg-config --cflags-only-I --static \$PACKAGES_NAMES ) -I{$iconv_prefix}/include" \
            LDFLAGS="\$(pkg-config  --libs-only-L   --static \$PACKAGES_NAMES ) -L{$iconv_prefix}/lib"  \
            LIBS="\$(pkg-config     --libs-only-l   --static \$PACKAGES_NAMES ) -liconv" \
            ./configure \
            --prefix={$odbc_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-readline \
            --enable-iconv \
            --enable-threads \
            --enable-gui=no

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$odbc_prefix}/lib/*.so.*
            rm -rf {$odbc_prefix}/lib/*.so
            rm -rf {$odbc_prefix}/lib/*.dylib
EOF
            )
            ->withDependentLibraries('readline', 'libiconv')
            ->withBinPath($odbc_prefix . '/bin/')
            ->withPkgName('odbc')
            ->withPkgName('odbccr')
            ->withPkgName('odbcinst')
    );
};
