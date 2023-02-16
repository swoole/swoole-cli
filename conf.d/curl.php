<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('cares'))
            ->withUrl('https://c-ares.org/download/c-ares-1.18.1.tar.gz')
            ->withPrefix('/usr/cares')
            ->withConfigure('./configure --prefix=/usr/cares --enable-static --disable-shared')
            ->withPkgName('libcares')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withHomePage('https://c-ares.org/')
    );
    $p->addLibrary(
        (new Library('curl'))
            ->withUrl('https://curl.se/download/curl-7.80.0.tar.gz')
            ->withPrefix('/usr/curl')
            ->withConfigure(
                "autoreconf -fi && ./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl " .
                "--without-librtmp --without-brotli --without-libidn2 --disable-ldap --disable-rtsp --without-zstd --without-nghttp2 --without-nghttp3"
            )
            ->withPkgName('libcurl')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withHomePage('https://curl.se/')
            ->depends('openssl', 'cares', 'zlib')
    );
    $p->addExtension((new Extension('curl'))->withOptions('--with-curl')->depends('curl'));
};
