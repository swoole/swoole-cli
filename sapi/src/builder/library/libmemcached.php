<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    // 静态编译报错
    // libmemcached/byteorder.cc:66:10: error: use of undeclared identifier 'ntohll'
    // libmemcached/byteorder.cc:75:10: error: use of undeclared identifier 'htonll'

    $libmemcached_prefix = LIBMEMCACHED_PREFIX;
    $lib = new Library('libmemcached');
    $lib->withHomePage('libmemcached.org/libMemcached.html')
        ->withLicense('https://libmemcached.org/License.html', Library::LICENSE_BSD)
        ->withManual('http://docs.libmemcached.org/')
        ->withUrl('https://launchpad.net/libmemcached/1.0/1.0.18/+download/libmemcached-1.0.18.tar.gz')
        ->withPrefix($libmemcached_prefix)
        ->withConfigure(
            <<<EOF

        ./configure --help
        ./configure \
        --prefix={$libmemcached_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --disable-sasl
        --without-mysql \
        --without-gearmand \
        --without-sphinx \
        --without-lcov \
        --without-genhtml

EOF
        );

    $p->addLibrary($lib);


};
