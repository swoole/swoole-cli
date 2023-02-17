<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('cares'))
            ->withUrl('https://c-ares.org/download/c-ares-1.19.0.tar.gz')
            ->withPrefix(CARES_PREFIX)
            ->withConfigure('./configure --prefix=' . CARES_PREFIX . ' --enable-static --disable-shared')
            ->withPkgName('libcares')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withHomePage('https://c-ares.org/')
    );
    $p->addLibrary(
        (new Library('curl'))
            ->withUrl('https://curl.se/download/curl-7.88.0.tar.gz')
            ->withPrefix(CURL_PREFIX)
            ->withConfigure(
                'autoreconf -fi && ./configure --prefix=' . CURL_PREFIX .
                ' --enable-static --disable-shared --with-openssl=/usr/openssl ' .
                '--without-librtmp --without-brotli --without-libidn2 --disable-ldap --disable-rtsp --without-zstd --without-nghttp2 --without-nghttp3'
            )
            ->withPkgName('libcurl')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withHomePage('https://curl.se/')
            ->depends('openssl', 'cares', 'zlib')
    );
    $p->addExtension((new Extension('curl'))->withOptions('--with-curl')->depends('curl'));
};
