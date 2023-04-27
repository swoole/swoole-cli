<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $brotli_prefix = BROTLI_PREFIX;
    $p->addLibrary(
        (new Library('brotli'))
            ->withHomePage('https://github.com/google/brotli')
            ->withManual('https://github.com/google/brotli')//有多种构建方式，选择cmake 构建
            ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
            ->withFile('brotli-1.0.9.tar.gz')
            ->withPrefix($brotli_prefix)
            ->withBuildScript(
                <<<EOF
            cmake . -DCMAKE_BUILD_TYPE=Release \
            -DCMAKE_INSTALL_PREFIX={$brotli_prefix} \
            -DBROTLI_SHARED_LIBS=OFF \
            -DBROTLI_STATIC_LIBS=ON \
            -DBROTLI_DISABLE_TESTS=OFF \
            -DBROTLI_BUNDLED_MODE=OFF \
            && \
            cmake --build . --config Release --target install
EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$brotli_prefix}/lib/*.so.*
            rm -rf {$brotli_prefix}/lib/*.so
            rm -rf {$brotli_prefix}/lib/*.dylib
            cp  -f {$brotli_prefix}/lib/libbrotlicommon-static.a {$brotli_prefix}/lib/libbrotli.a
            mv     {$brotli_prefix}/lib/libbrotlicommon-static.a {$brotli_prefix}/lib/libbrotlicommon.a
            mv     {$brotli_prefix}/lib/libbrotlienc-static.a    {$brotli_prefix}/lib/libbrotlienc.a
            mv     {$brotli_prefix}/lib/libbrotlidec-static.a    {$brotli_prefix}/lib/libbrotlidec.a
EOF
            )
            ->withPkgName('libbrotlicommon')
            ->withPkgName('libbrotlidec')
            ->withPkgName('libbrotlienc')
            ->withBinPath($brotli_prefix . '/bin/')
    );

    $p->addLibrary(
        (new Library('cares'))
            ->withHomePage('https://c-ares.org/')
            ->withManual('https://c-ares.org/')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withUrl('https://c-ares.org/download/c-ares-1.19.0.tar.gz')
            ->withPrefix(CARES_PREFIX)
            ->withConfigure('./configure --prefix=' . CARES_PREFIX . ' --enable-static --disable-shared')
            ->withPkgName('libcares')
    );

    $libiconv_prefix = ICONV_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    if (0) {
        $p->addLibrary(
            (new Library('libunistring'))
                ->withHomePage('https://www.gnu.org/software/libunistring/')
                ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
                ->withUrl('https://ftp.gnu.org/gnu/libunistring/libunistring-1.1.tar.gz')
                ->withPrefix($libunistring_prefix)
                ->withConfigure(
                    <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libunistring_prefix} \
            --with-libiconv-prefix={$libiconv_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
                )
        );
        $libidn2_prefix = LIBIDN2_PREFIX;
        $p->addLibrary(
            (new Library('libidn2'))
                ->withHomePage('https://gitlab.com/libidn/libidn2')
                ->withManual('https://www.gnu.org/software/libidn/libidn2/manual/')
                ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
                ->withUrl('https://ftp.gnu.org/gnu/libidn/libidn2-2.3.4.tar.gz')
                ->withPrefix($libidn2_prefix)
                ->withConfigure(
                    <<<EOF
            ./configure --help
            ./configure --prefix={$libidn2_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --disable-doc \
            --with-libiconv-prefix={$libiconv_prefix} \
            --with-libunistring-prefix={$libunistring_prefix} \
            --without-libintl-prefix

EOF
                )
                ->withPkgName('libidn2')
                ->depends('libiconv', 'libunistring')
        );
    }
    $nghttp2_prefix = NGHTTP2_PREFIX;
    $p->addLibrary(
        (new Library('nghttp2'))
            ->withHomePage('https://github.com/nghttp2/nghttp2.git')
            ->withManual('https://nghttp2.org/')
            ->withLicense('https://github.com/nghttp2/nghttp2/blob/master/COPYING', Library::LICENSE_MIT)
            ->withUrl('https://github.com/nghttp2/nghttp2/releases/download/v1.51.0/nghttp2-1.51.0.tar.gz')
            ->withPrefix($nghttp2_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            packages="zlib libxml-2.0 libcares openssl "  # jansson  libev libbpf libelf libngtcp2 libnghttp3
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$packages )"  \
            LDFLAGS="$(pkg-config --libs-only-L      --static \$packages )"  \
            LIBS="$(pkg-config --libs-only-l         --static \$packages )"  \
            ./configure --prefix={$nghttp2_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-lib-only \
            --with-libxml2  \
            --with-zlib \
            --with-libcares \
            --with-openssl \
            --disable-http3 \
            --disable-python-bindings  \
            --without-jansson  \
            --without-libevent-openssl \
            --without-libev \
            --without-cunit \
            --without-jemalloc \
            --without-mruby \
            --without-neverbleed \
            --without-cython \
            --without-libngtcp2 \
            --without-libnghttp3  \
            --without-libbpf   \
            --with-boost=no
EOF
            )
            ->withPkgName('libnghttp2')
            ->depends('openssl', 'zlib', 'libxml2', 'cares')
    );

    $libssh2_prefix = LIBSSH2_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $p->addLibrary(
        (new Library('libssh2'))
            ->withHomePage('https://www.libssh2.org/')
            ->withLicense('https://www.libssh2.org/license.html', Library::LICENSE_SPEC)
            ->withManual('https://github.com/libssh2/libssh2.git')
            ->withManual('https://github.com/libssh2/libssh2/blob/master/docs/INSTALL_CMAKE.md')
            ->withUrl('https://www.libssh2.org/download/libssh2-1.10.0.tar.gz')
            ->withPrefix($libssh2_prefix)
            ->withBuildScript(
                <<<EOF
              mkdir -p build
              cd build
              cmake .. \
              -DCMAKE_INSTALL_PREFIX={$libssh2_prefix} \
              -DCMAKE_BUILD_TYPE=Release  \
              -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
              -DBUILD_STATIC_LIBS=ON \
              -DBUILD_SHARED_LIBS=OFF \
              -DENABLE_ZLIB_COMPRESSION=ON  \
              -DZLIB_ROOT={$zlib_prefix} \
              -DCLEAR_MEMORY=ON  \
              -DENABLE_GEX_NEW=ON  \
              -DENABLE_CRYPT_NONE=OFF  \
              -DOpenSSL_ROOT={$openssl_prefix} \
              -DCRYPTO_BACKEND=OpenSSL \
              -DBUILD_TESTING=OFF \
              -DBUILD_EXAMPLES=OFF
              cmake --build . --target install
EOF
            )
            ->withPkgName('libssh2')
            ->depends('zlib', 'openssl')
    );

    $curl_prefix = CURL_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $cares_prefix = CARES_PREFIX;

    $p->addLibrary(
        (new Library('curl'))
            ->withHomePage('https://curl.se/')
            ->withManual('https://curl.se/docs/install.html')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withUrl('https://curl.se/download/curl-7.88.0.tar.gz')
            ->withPrefix($curl_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help

            PACKAGES='zlib openssl libcares libbrotlicommon libbrotlidec libbrotlienc libzstd libnghttp2 '
            PACKAGES="\$PACKAGES  libssh2" # libidn2
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure --prefix={$curl_prefix}  \
            --enable-static \
            --disable-shared \
            --without-librtmp \
            --disable-ldap \
            --disable-rtsp \
            --enable-http \
            --enable-alt-svc \
            --enable-hsts \
            --enable-http-auth \
            --enable-mime \
            --enable-cookies \
            --enable-doh \
            --enable-threaded-resolver \
            --enable-ipv6 \
            --enable-proxy  \
            --enable-websockets \
            --enable-get-easy-options \
            --enable-file \
            --enable-mqtt \
            --enable-unix-sockets  \
            --enable-progress-meter \
            --enable-optimize \
            --with-zlib={$zlib_prefix} \
            --with-openssl={$openssl_prefix} \
            --enable-ares={$cares_prefix} \
            --with-default-ssl-backend=openssl \
            --with-libidn2 \
            --with-libssh2 \
            --with-nghttp2 \
            --without-ngtcp2 \
            --without-nghttp3

EOF
            )
            ->withPkgName('libcurl')
            ->withBinPath($curl_prefix . '/bin/')
            ->depends('openssl', 'cares', 'zlib', 'brotli', 'libzstd', 'nghttp2', 'libssh2') #'libidn2',
    );
    $p->addExtension(
        (new Extension('curl'))
            ->withHomePage('https://www.php.net/curl')
            ->withOptions('--with-curl')
            ->depends('curl')
    );
};
