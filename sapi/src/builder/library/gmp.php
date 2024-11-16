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
            ->withUrl('https://ftp.gnu.org/gnu/gmp/gmp-6.3.0.tar.lz')
            ->withFileHash('md5', 'db3f4050677df3ff2bd23422c0d3caa1')
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
