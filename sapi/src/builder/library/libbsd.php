<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libbsd_prefix = LIBBSD_PREFIX;
    $lib = new Library('libbsd');
    $lib->withHomePage('https://libbsd.freedesktop.org/wiki/')
        ->withLicense('https://spdx.org/licenses/BSD-3-Clause.html', Library::LICENSE_BSD)
        ->withManual('https://libbsd.freedesktop.org/wiki/')
        # ->withFile('libbsd-latest.tar.gz')
        ->withFile('libbsd-v0.11.7.tar.gz')
        ->withDownloadScript(
            'libbsd',
            <<<EOF

               #  git clone -b main  --depth=1 https://gitlab.freedesktop.org/libbsd/libbsd.git
               #  git clone -b main  --depth=1 https://anongit.freedesktop.org/git/libbsd.git

                git clone -b 0.11.7  --depth=1 https://anongit.freedesktop.org/git/libbsd.git

EOF
        )
        ->withPrefix($libbsd_prefix)
        ->withConfigure(
            <<<EOF
            sh autogen
            ./configure --help

            PACKAGES='libmd '
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) " \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) " \
            ./configure \
            --prefix={$libbsd_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        ->withPkgName('libbsd')
        ->withPkgName('libbsd-overlay')
        ->withDependentLibraries('libmd');

    $p->addLibrary($lib);
};
