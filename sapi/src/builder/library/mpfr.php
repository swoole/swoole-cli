<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $mpfr_prefix = MPFR_PREFIX;
    $gmp_prefix = GMP_PREFIX;
    $p->addLibrary(
        (new Library('mpfr'))
            ->withHomePage('https://www.mpfr.org/')
            ->withManual('https://www.mpfr.org/mpfr-current/mpfr.html')
            ->withLicense('https://www.gnu.org/licenses/lgpl-3.0.html', Library::LICENSE_LGPL)
            ->withUrl('https://www.mpfr.org/mpfr-current/mpfr-4.2.2.tar.xz')
            ->withFileHash('md5', '7c32c39b8b6e3ae85f25156228156061')
            ->withPrefix($mpfr_prefix)
            ->withDependentLibraries('gmp')
            ->withConfigure(
                <<<EOF
            ./configure --help

            CFLAGS="-fPIC" \
            ./configure \
            --prefix={$mpfr_prefix} \
            --with-gmp={$gmp_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --with-pic

EOF
            )
            ->withPkgName('mpfr')
    );
};
