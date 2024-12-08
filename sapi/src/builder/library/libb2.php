<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libb2_prefix = LIBB2_PREFIX;

    $lib = new Library('libb2');
    $lib->withHomePage('https://github.com/BLAKE2/libb2.git')
        ->withLicense('https://github.com/BLAKE2/libb2#CC0-1.0-1-ov-file', Library::LICENSE_SPEC)
        ->withManual('https://github.com/BLAKE2/libb2.git')
        ->withUrl('https://github.com/BLAKE2/libb2/archive/refs/tags/v0.98.1.tar.gz')
        ->withFile('libb2-v0.98.1.tar.gz')
        ->withPrefix($libb2_prefix)
        ->withConfigure(
            <<<EOF
        sh autogen.sh
        ./configure --help

        ./configure \
        --prefix={$libb2_prefix} \
        --enable-shared=no \
        --enable-static=yes
EOF
        )
        ->withPkgName('libb2');

    $p->addLibrary($lib);
};
