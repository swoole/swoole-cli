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
            ->withConfigure(
                <<<EOF
            ./configure --help
            PACKAGES='ncursesw  '
            CFLAGS=' -D__STDC_ISO_10646__=201103L ' \
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) " \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) " \
            ./configure \
            --prefix={$libedit_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-examples=no \
            --enable-widec

EOF
            )
            ->withPkgName('libedit')
            ->withDependentLibraries('ncurses')
    );
};
