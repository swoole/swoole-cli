<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libedit_prefix = LIBEDIT_PREFIX;
    $p->addLibrary(
        (new Library('libedit'))
            ->withHomePage('https://thrysoee.dk/editline/')
            ->withLicense('http://www.netbsd.org/Goals/redistribution.html', Library::LICENSE_BSD)
            ->withUrl('https://thrysoee.dk/editline/libedit-20230828-3.1.tar.gz')
            ->withFileHash('md5', '16bb2ab0d33bce3467f5cd4ec7d8f3ee')
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
