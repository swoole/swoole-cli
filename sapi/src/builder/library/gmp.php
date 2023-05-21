<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $gmp_prefix = GMP_PREFIX;
    $p->addLibrary(
        (new Library('gmp'))
            ->withHomePage('https://gmplib.org/')
            ->withManual('https://gmplib.org/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withUrl('https://gmplib.org/download/gmp/gmp-6.2.1.tar.lz')
            ->withPrefix($gmp_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix=$gmp_prefix \
            --enable-static=yes \
            --enable-shared=no
EOF
            )
            ->withPkgName('gmp')
    );
};
