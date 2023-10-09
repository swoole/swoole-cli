<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmicrohttpd_prefix = LIBMICROHTTPD_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $curl_prefix = CURL_PREFIX;
    $lib = new Library('libmicrohttpd');
    $lib->withHomePage('https://www.gnu.org/software/libmicrohttpd/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://ftp.gnu.org/gnu/libmicrohttpd/libmicrohttpd-0.9.77.tar.gz')
        ->withManual('https://www.gnu.org/software/libmicrohttpd/')
        ->withPrefix($libmicrohttpd_prefix)
        ->withConfigure(
            <<<EOF
        ./configure --help
        ./configure \
        --prefix={$libmicrohttpd_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --disable-doc \
        --disable-examples \
        --enable-poll \
        --enable-epoll \
        --with-libiconv-prefix={$libiconv_prefix} \
        --with-libcurl={$curl_prefix}



EOF
        )
        ->withPkgName('libmicrohttpd')
        ->withDependentLibraries('curl', 'libiconv')

    ;

    $p->addLibrary($lib);
};
