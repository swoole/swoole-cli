<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $liburing_prefix = LIBURING_PREFIX;
    $p->addLibrary(
        (new Library('liburing')) # io_uring iouring
        ->withHomePage('https://github.com/axboe/liburing.git')
            ->withLicense('https://github.com/axboe/liburing/blob/master/COPYING', Library::LICENSE_LGPL)
            ->withManual('hhttps://github.com/axboe/liburing')
            ->withUrl('https://github.com/axboe/liburing/archive/refs/tags/liburing-2.6.tar.gz')
            ->withPrefix($liburing_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help
                ./configure \
                --prefix={$liburing_prefix} \
                --use-libc

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$liburing_prefix}/lib/*.so.*
            rm -rf {$liburing_prefix}/lib/*.so
            rm -rf {$liburing_prefix}/lib/*.dylib
EOF
            )
            ->withBinPath($liburing_prefix . '/bin/')
            ->withPkgName('liburing')
    );
};
