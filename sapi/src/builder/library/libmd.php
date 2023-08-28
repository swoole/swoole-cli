<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmd_prefix = LIBMD_PREFIX;

    $lib = new Library('libmd');
    $lib->withHomePage('https://www.hadrons.org/software/libmd/')
        ->withLicense('https://spdx.org/licenses/BSD-3-Clause.html', Library::LICENSE_BSD)
        ->withManual('https://www.hadrons.org/software/libmd/')
        ->withFile('libmd-v1.1.0.tar.gz')
        ->withDownloadScript(
            'libmd',
            <<<EOF
            git clone -b 1.1.0  --depth=1  https://git.hadrons.org/git/libmd.git
EOF
        )
        ->withPrefix($libmd_prefix)
        ->withConfigure(
            <<<EOF
            sh autogen
            ./configure --help

            ./configure \
            --prefix={$libmd_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        ->withPkgName('libmd');

    $p->addLibrary($lib);
};
