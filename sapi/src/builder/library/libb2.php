<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libb2_prefix = LIBB2_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('libb2');
    $lib->withHomePage('https://github.com/BLAKE2/libb2.git')
        ->withLicense('https://github.com/BLAKE2/libb2#CC0-1.0-1-ov-file', Library::LICENSE_SPEC)
        ->withManual('https://github.com/BLAKE2/libb2.git')
        ->withFile('libb2-v0.98.1.tar.gz')
        ->withDownloadScript(
            'libb2',
            <<<EOF
                git clone -b v0.98.1  --depth=1 https://github.com/BLAKE2/libb2.git
EOF
        )
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
