<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $gmp_prefix = GMP_PREFIX;
    $configureEnvironment = $p->isMobileTarget()
        ? 'CFLAGS="$CFLAGS -fPIC" CXXFLAGS="$CXXFLAGS -fPIC"'
        : 'CFLAGS="-fPIC"';
    $targetOptions = $p->isMobileTarget()
        ? " \\\n            --host=aarch64-apple-darwin \\\n            --disable-assembly"
        : '';
    if ($p->isAndroid()) {
        $targetOptions = str_replace('aarch64-apple-darwin', 'aarch64-linux-android', $targetOptions);
    }
    $p->addLibrary(
        (new Library('gmp'))
            ->withHomePage('https://gmplib.org/')
            ->withManual('https://gmplib.org/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withUrl('https://ftp.gnu.org/gnu/gmp/gmp-6.3.0.tar.xz')
            ->withFileHash('md5', '956dc04e864001a9c22429f761f2c283')
            ->withPrefix($gmp_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help

            {$configureEnvironment} \
            ./configure \
            --prefix=$gmp_prefix \
            --enable-static=yes \
            --enable-shared=no \
            --enable-cxx \
            --with-pic{$targetOptions}

EOF
            )
            ->withPkgName('gmp')
            ->withPkgName('gmpxx')
    );
};
