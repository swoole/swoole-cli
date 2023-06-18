<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $aria2_prefix = ARIA2_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('aria2'))
            ->withHomePage('https://aria2.github.io/')
            ->withLicense('https://github.com/aria2/aria2/blob/master/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://github.com/aria2/aria2/releases/download/release-1.36.0/aria2-1.36.0.tar.gz')
            ->withManual('https://aria2.github.io/manual/en/html/README.html')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            ./configure --help
            set -x
            PACKAGES='zlib openssl sqlite3 nettle libxml-2.0 libcares'
            PACKAGES="\$PACKAGES  libssh2 libuv"
            CPPFLAGS="-I{$libiconv_prefix}/include"
            LDFLAGS="-L{$libiconv_prefix}/lib"
            LIBS="-liconv"
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
            --with-libz
            # --with-tcmalloc
EOF
            )
            ->withBinPath($aria2_prefix . '/bin/')
            ->withDependentLibraries('libuv', 'zlib', 'libiconv', 'openssl', 'sqlite3', 'nettle', 'libxml2', 'cares', 'libssh2')
    );
};
