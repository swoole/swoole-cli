<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libde265_prefix = LIBDE265_PREFIX;
    $lib = new Library('libde265');
    $lib->withHomePage('https://github.com/strukturag/libde265.git')
        ->withLicense('https://github.com/strukturag/libde265/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/strukturag/libde265.git')
        ->withFile('libde265-v1.0.15.tar.gz')
        ->withDownloadScript(
            'libde265',
            <<<EOF
                git clone -b v1.0.15  --depth=1 https://github.com/strukturag/libde265.git
EOF
        )
        ->withPrefix($libde265_prefix)
        ->withConfigure(
            <<<EOF
        sh autogen.sh

        ./configure --help

        ./configure \
        --prefix={$libde265_prefix} \
        --enable-shared=no \
        --enable-static=yes

EOF
        )
        ->withPkgName('libde265')
        ->withBinPath($libde265_prefix . '/bin/');

    $p->addLibrary($lib);
};
