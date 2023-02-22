<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $cares_prefix = CARES_PREFIX   ;
    $p->addLibrary(
        (new Library('cares'))
            ->withUrl('https://c-ares.org/download/c-ares-1.19.0.tar.gz')
            ->withPrefix($cares_prefix)
            ->withConfigure("./configure --prefix={$cares_prefix} --enable-static --disable-shared")
            ->withPkgName('libcares')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withHomePage('https://c-ares.org/')
    );
    $brotli_prefix = BROTLI_PREFIX;
    $p->addLibrary(
        (new Library('brotli'))
            ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
            ->withFile('brotli-1.0.9.tar.gz')
            ->withPrefix($brotli_prefix)
            ->withConfigure(<<<EOF
            cmake . -DCMAKE_BUILD_TYPE=Release \
            -DCMAKE_INSTALL_PREFIX={$brotli_prefix} \
            -DBROTLI_SHARED_LIBS=OFF \
            -DBROTLI_STATIC_LIBS=ON \
            -DBROTLI_DISABLE_TESTS=ON \
            -DBROTLI_BUNDLED_MODE=OFF 
                
            cmake --build . --config Release --target install
EOF
            )
            ->withSkipMakeAndMakeInstall()
            ->withScriptAfterInstall(
                implode(PHP_EOL, [
                    'rm -rf ' . BROTLI_PREFIX . '/lib/*.so.*',
                    'rm -rf ' . BROTLI_PREFIX . '/lib/*.so',
                    'rm -rf ' . BROTLI_PREFIX . '/lib/*.dylib',
                    'cp ' . BROTLI_PREFIX . '/lib/libbrotlicommon-static.a ' . BROTLI_PREFIX . '/lib/libbrotli.a',
                    'mv ' . BROTLI_PREFIX . '/lib/libbrotlicommon-static.a ' . BROTLI_PREFIX . '/lib/libbrotlicommon.a',
                    'mv ' . BROTLI_PREFIX . '/lib/libbrotlienc-static.a ' . BROTLI_PREFIX . '/lib/libbrotlienc.a',
                    'mv ' . BROTLI_PREFIX . '/lib/libbrotlidec-static.a ' . BROTLI_PREFIX . '/lib/libbrotlidec.a'
                ]))
            ->withPkgName('libbrotlicommon libbrotlidec libbrotlienc')
            ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/google/brotli')
    );

    $libidn2_prefix = LIBIDN2_PREFIX;
    $p->addLibrary(
        (new Library('libidn2'))
            ->withUrl('https://ftp.gnu.org/gnu/libidn/libidn2-2.3.4.tar.gz')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withPrefix($libidn2_prefix)
            ->withConfigure(<<<EOF
            ./configure --help 
            
            #  intl  依赖  gettext
            # 解决依赖  apk add  gettext  coreutils
            
            ./configure --prefix={$libidn2_prefix} \
            enable_static=yes \
            enable_shared=no \
            --disable-doc \
            --with-libiconv-prefix=/usr/libiconv \
            --with-libintl-prefix
             
EOF
            )
            ->withPkgName('libidn2')
            ->depends('libiconv')
    );

    //http3 有多个实现
    //参考文档： https://curl.se/docs/http3.html
    //https://curl.se/docs/protdocs.html
    $curl_prefix = CURL_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX ;

    $libidn2_prefix = LIBIDN2_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $cares_prefix = CARES_PREFIX;
    $brotli_prefix = BROTLI_PREFIX;
    $p->addLibrary(
        (new Library('curl'))
            ->withHomePage('https://curl.se/')
            ->withUrl('https://curl.se/download/curl-7.88.0.tar.gz')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withPrefix($curl_prefix)
            ->withConfigure(<<<EOF
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static zlib libbrotlicommon  libbrotlidec  libbrotlienc openssl libcares libidn2 )" \
            LDFLAGS="$(pkg-config --libs-only-L      --static zlib libbrotlicommon  libbrotlidec  libbrotlienc openssl libcares libidn2 )" \
            LIBS="$(pkg-config --libs-only-l         --static zlib libbrotlicommon  libbrotlidec  libbrotlienc openssl libcares libidn2 )" \
            ./configure --prefix={$curl_prefix}  \
            --enable-static --disable-shared \
            --without-librtmp --disable-ldap --disable-rtsp \
            --enable-http --enable-alt-svc --enable-hsts --enable-http-auth --enable-mime --enable-cookies \
            --enable-doh --enable-threaded-resolver --enable-ipv6 --enable-proxy  \
            --enable-websockets --enable-get-easy-options \
            --enable-file --enable-mqtt --enable-unix-sockets  --enable-progress-meter \
            --enable-optimize \
            --with-zlib={$zlib_prefix} \
            --with-openssl={$openssl_prefix} \
            --with-libidn2={$libidn2_prefix} \
            --with-zstd={$libzstd_prefix} \
            --enable-ares={$cares_prefix} \
            --with-brotli={$brotli_prefix} \
            --with-default-ssl-backend=openssl \
            --without-nghttp2 \
            --without-ngtcp2 \
            --without-nghttp3 
EOF
            )
            ->withPkgName('libcurl')
            ->depends('openssl', 'cares', 'zlib','brotli','libzstd','libidn2')
    );
    $p->addExtension((new Extension('curl'))->withOptions('--with-curl')->depends('curl'));

};
