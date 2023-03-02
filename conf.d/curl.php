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
    $libidn2_prefix = LIBIDN2_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libidn2'))
            ->withUrl('https://ftp.gnu.org/gnu/libidn/libidn2-2.3.4.tar.gz')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withPrefix($libidn2_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help 
            
            # 依赖 intl libunistring ； (gettext库包含intl 、coreutils库包含libunistring )
            # 解决依赖  apk add  gettext  coreutils
            
            ./configure --prefix={$libidn2_prefix} \
            enable_static=yes \
            enable_shared=no \
            --disable-doc \
            --with-libiconv-prefix={$libiconv_prefix} \
            --with-libintl-prefix
             
EOF
            )
            ->withScriptAfterInstall("
            # 查看是否有动态链接库 (已确认，无动态链接库）
            nm -D {$libidn2_prefix}/lib/libidn2.a
            # nm {$libidn2_prefix}/lib/libidn2.a
            ar -t {$libidn2_prefix}/lib/libidn2.a
            ")
            ->withPkgName('libidn2')
            ->depends('libiconv')
    );

    $p->addLibrary(
        (new Library('curl'))
            ->withUrl('https://curl.se/download/curl-7.88.0.tar.gz')
            ->withPrefix(CURL_PREFIX)
            ->withConfigure(
                'autoreconf -fi && ./configure --prefix=' . CURL_PREFIX .
                ' --enable-static --disable-shared --with-openssl=' . OPENSSL_PREFIX . ' ' .
                '--without-librtmp --without-brotli --without-libidn2 --disable-ldap --disable-rtsp --without-zstd --without-nghttp2 --without-nghttp3'
            )
            ->withPkgName('libcurl')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withHomePage('https://curl.se/')
            ->depends('openssl', 'cares', 'zlib')
    );
    $p->addExtension((new Extension('curl'))->withOptions('--with-curl')->depends('curl'));
};
