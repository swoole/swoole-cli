<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libde265_prefix = LIBDE265_PREFIX;
    $lib = new Library('libde265');
    $lib->withHomePage('https://github.com/strukturag/libde265.git')
        ->withLicense('https://github.com/strukturag/libde265/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/strukturag/libde265.git')
        ->withUrl('https://github.com/strukturag/libde265/releases/download/v1.0.16/libde265-1.0.16.tar.gz')
        ->withPrefix($libde265_prefix)
        ->withConfigure(
            <<<EOF
        sh autogen.sh

        ./configure --help

        ./configure \
        --prefix={$libde265_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --enable-pic \
        --enable-encoder \
        --disable-sherlock265

EOF
        )
        ->withPkgName('libde265')
        ->withBinPath($libde265_prefix . '/bin/');

    $p->addLibrary($lib);
};
