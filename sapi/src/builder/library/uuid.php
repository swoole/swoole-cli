<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $lib = new Library('uuid');
    $lib->withHomePage('http://www.ossp.org/pkg/lib/uuid/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('ftp://ftp.ossp.org/pkg/lib/uuid/uuid-1.6.1.tar.gz')
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)
        ->withBuildLibraryCached(false)
        ->withConfigure(
            <<<EOF
            libtoolize -ci
            autoreconf -fi
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -static" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withSkipDownload()
        ->withPkgName('ssl')
        ->withBinPath($example_prefix . '/bin/');

    $p->addLibrary($lib);
};
