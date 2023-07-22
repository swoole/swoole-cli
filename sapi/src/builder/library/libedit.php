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
            # autoreconf -fi
            ./configure --help
            ./configure \
            --prefix={$libedit_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-examples=no

EOF
            )
            ->withPkgName('libedit')

    );
};
