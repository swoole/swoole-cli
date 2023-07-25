<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $apr_prefix = APR_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('apr');
    $lib->withHomePage('https://apr.apache.org/')
        ->withLicense('https://www.apache.org/licenses/', Library::LICENSE_APACHE2)
        ->withUrl('https://dlcdn.apache.org//apr/apr-1.7.4.tar.gz')
        ->withManual('https://apr.apache.org/compiling_unix.html')
        ->withPrefix($apr_prefix)
        ->withConfigure(
            <<<EOF
           ./configure --help
           ./configure \
           --prefix={$apr_prefix} \
           --with-devrandom \
           --enable-threads \
           --enable-posix-shm \
           --enable-sysv-shm

EOF
        )
        ->withPkgName('apr-1')
        ->withBinPath($apr_prefix . '/bin/')
    ;

    $p->addLibrary($lib);


};
