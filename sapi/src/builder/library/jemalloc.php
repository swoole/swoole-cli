<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $jemallocl_prefix = JEMALLOC_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $p->addLibrary(
        (new Library('jemalloc'))
            ->withHomePage('http://jemalloc.net/')
            ->withLicense(
                'https://github.com/jemalloc/jemalloc/blob/dev/COPYING',
                Library::LICENSE_GPL
            )
            ->withUrl('https://github.com/jemalloc/jemalloc/archive/refs/tags/5.3.0.tar.gz')
            ->withFile('jemalloc-5.3.0.tar.gz')
            ->withPrefix($jemallocl_prefix)
            ->withConfigure(
                <<<EOF
            sh autogen.sh
            ./configure --help ;
            ./configure \
            --prefix={$jemallocl_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --with-static-libunwind={$libunistring_prefix}
EOF
            )
            ->withPkgName('jemalloc')
    );
};
