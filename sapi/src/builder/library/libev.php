<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libev_prefix = LIBEV_PREFIX;
    $p->addLibrary(
        (new Library('libev'))
            ->withHomePage('http://software.schmorp.de/pkg/libev.html')
            ->withLicense('http://cvs.schmorp.de/libev/README', Library::LICENSE_BSD)
            ->withUrl('http://dist.schmorp.de/libev/libev-4.33.tar.gz')
            ->withManual('http://cvs.schmorp.de/libev/README')
            ->withPrefix($libev_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libev_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libev_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
            )
    );
};

/*
 *  LIBS="$LIBS -lev"
 */
