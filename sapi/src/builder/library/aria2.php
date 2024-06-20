<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $aria2_prefix = ARIA2_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $libintl_prefix = LIBINTL_PREFIX;
    $p->addLibrary(
        (new Library('aria2'))
            ->withHomePage('https://aria2.github.io/')
            ->withLicense('https://github.com/aria2/aria2/blob/master/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://github.com/aria2/aria2/releases/download/release-1.37.0/aria2-1.37.0.tar.gz')
            ->withManual('https://aria2.github.io/manual/en/html/README.html')
            ->withInstallCached(false)
            ->withBuildCached(false)
            ->withConfigure(
                <<<EOF
            ./configure --help
            set -x
            PACKAGES='zlib openssl sqlite3 nettle libxml-2.0 libcares'
            PACKAGES="\$PACKAGES  libssh2 libuv"
            PACKAGES="\$PACKAGES gmp"
            PACKAGES="\$PACKAGES expat"
            PACKAGES="\$PACKAGES libssh2"
            PACKAGES="\$PACKAGES nettle hogweed"
            CPPFLAGS="-I{$libiconv_prefix}/include -I{$libintl_prefix}/include "
            LDFLAGS="-L{$libiconv_prefix}/lib -L{$libintl_prefix}/lib"
            LIBS="-liconv -lintl"
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) \$CPPFLAGS " \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) \$LDFLAGS " \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) \$LIBS " \
            ARIA2_STATIC=yes \
            ./configure \
            --with-ca-bundle="/etc/ssl/certs/ca-certificates.crt" \
            --prefix={$aria2_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-libaria2 \
            --with-libuv \
            --without-gnutls \
            --with-openssl \
            --with-libiconv-prefix={$libiconv_prefix} \
            --with-libz \
            --with-libssh2
            --without-appletls \
            --without-wintls \
            --without-gnutls \
            --without-libgcrypt
            # --with-tcmalloc
EOF
            )
            ->withBinPath($aria2_prefix . '/bin/')
            ->withDependentLibraries(
                'libuv',
                'zlib',
                'libiconv',
                'openssl',
                'sqlite3',
                'nettle',
                'libxml2',
                'cares',
                'libssh2',
                'gmp',
                'libexpat',
                'libintl',
                'libssh2',
                'nettle'
            )
    );
};
