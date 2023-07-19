<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libedit_prefix = LIBEDIT_PREFIX;
    $p->addLibrary(
        (new Library('libedit'))
            ->withLicense('http://www.netbsd.org/Goals/redistribution.html', Library::LICENSE_BSD)
            ->withHomePage('https://thrysoee.dk/editline/')
            ->withUrl('https://thrysoee.dk/editline/libedit-20210910-3.1.tar.gz')
            ->withPrefix($libedit_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libedit_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            autoreconf -fi
            PACKAGES='ncursesw '
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -static" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$libedit_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-examples=no

EOF
            )
            ->withLdflags('')
            ->withBuildLibraryCached(false)
            ->withDependentLibraries('ncurses')
    );
};
