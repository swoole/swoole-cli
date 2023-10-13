<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = OPENCV_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('opensips');
    $lib->withHomePage('https://www.opensips.org/')
        ->withLicense('https://github.com/OpenSIPS/opensips/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('https://github.com/OpenSIPS/opensips.git')
        ->withManual('https://github.com/OpenSIPS/opensips/blob/master/INSTALL')
        ->withFile('opensips-latest.tar.gz')
        ->withDownloadScript(
            'opensips',
            <<<EOF
                git clone -b master --depth=1 --recursive https://github.com/OpenSIPS/opensips.git
EOF
        )
        ->withBuildCached(false)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            # apk add uuid-runtime
EOF
        )

        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)

        ->withConfigure(
            <<<EOF
            libtoolize -ci
            autoreconf -fi
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"
            PACKAGES="\$PACKAGES libsctp"
            PACKAGES="\$PACKAGES libpq"
            PACKAGES="\$PACKAGES odbc odbccr odbcinst"
            PACKAGES="\$PACKAGES odbcinst"
            PACKAGES="\$PACKAGES libxml-2.0"
            PACKAGES="\$PACKAGES ncursesw"


            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )

        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries(
            'zlib',
            'openssl',
            'libsctp',
            'pgsql',
            'unixODBC',
            'libexpat',
            'libxml2',
            // 'libradius-ng', //待解决
            // 'libsnmp', //待解决
            // 'libldap', //待解决
            'ncurses'
        )
    ;

    $p->addLibrary($lib);
};
