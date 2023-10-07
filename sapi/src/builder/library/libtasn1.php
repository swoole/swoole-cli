<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libtasn1_prefix = LIBTASN1_PREFIX;
    $p->addLibrary(
        (new Library('libtasn1'))
            ->withHomePage('https://www.gnu.org/software/libtasn1/')
            ->withLicense('https://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/libtasn1/manual/')
            //->withUrl('https://ftp.gnu.org/gnu/libtasn1/libtasn1-4.19.0.tar.gz')
            ->withUrl('https://mirrors.aliyun.com//gnu/libtasn1/libtasn1-4.19.0.tar.gz')
            ->withHttpProxy(false)
            //->withAutoUpdateFile()
            ->withPrefix($libtasn1_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libtasn1_prefix} \
            --enable-static=yes \
            --enable-shared=no
EOF
            )
            ->withPkgName('libtasn1')
    );
};
