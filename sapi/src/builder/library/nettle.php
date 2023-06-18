<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $nettle_prefix = NETTLE_PREFIX;
    $p->addLibrary(
        (new Library('nettle'))
            ->withHomePage('https://www.lysator.liu.se/~nisse/nettle/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://ftp.gnu.org/gnu/nettle/nettle-3.8.tar.gz')
            ->withFile('nettle-3.8.tar.gz')
            ->withPrefix($nettle_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($nettle_prefix)
            ->withConfigure(
                <<<EOF
             ./configure --help
            ./configure \
            --prefix={$nettle_prefix} \
            --enable-static \
            --disable-shared \
            --enable-mini-gmp
EOF
            )
            ->withPkgName('nettle')
            ->withDependentLibraries('gmp')
    );
};
