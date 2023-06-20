<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
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
            ->withDependentLibraries('openssl', 'zlib', 'libxml2', 'cares')
    );
};
