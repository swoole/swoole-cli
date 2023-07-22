<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $freeswitch_prefix = FREESWITCH_PREFIX;
    $odbc_prefix = UNIX_ODBC_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $lib = new Library('freeswitch');
    $lib->withHomePage('https://github.com/signalwire/freeswitch.git')
        ->withLicense('https://github.com/signalwire/freeswitch/blob/master/LICENSE', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/signalwire/freeswitch/archive/refs/tags/v1.10.9.tar.gz')
        ->withManual('https://freeswitch.com/#getting-started')
        ->withFile('freeswitch-v1.10.9.tar.gz')
        ->withDownloadScript(
            'freeswitch',
            <<<EOF
                git clone -b v1.10.9  --depth=1 https://github.com/signalwire/freeswitch.git
EOF
        )
        ->withPrefix($freeswitch_prefix)
        ->withBuildLibraryCached(false)
        ->withPreInstallCommand(
            <<<EOF
        apt install libtool  libtool-bin
EOF
        )
        ->withCleanBuildDirectory()
        ->withBuildScript(
            <<<EOF
            ./bootstrap.sh

            ./configure --help
            # CFLAGS="-O3 -std=c11 -g " \
            PACKAGES="openssl libpq spandsp sofia-sip-ua odbc libjpeg libturbojpeg liblzma libpng sqlite3 zlib libcurl"
            PACKAGES="\$PACKAGES libcares  libbrotlicommon libbrotlidec libbrotlienc"
            PACKAGES="\$PACKAGES libnghttp2 libnghttp3 "
            PACKAGES="\$PACKAGES libngtcp2 libngtcp2_crypto_openssl "
            PACKAGES="\$PACKAGES libpcre  libpcre16  libpcre32  libpcrecpp  libpcreposix "
            PACKAGES="\$PACKAGES speex speexdsp "
            PACKAGES="\$PACKAGES yaml-0.1 "
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) -I{$libtiff_prefix}/include -I{$bzip2_prefix}/include" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) -L{$libtiff_prefix}/lib -L{$bzip2_prefix}/lib" \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES )" \
            ./configure \
            --prefix={$freeswitch_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-optimization \
            --with-openssl \
            --with-python3 \
            --with-odbc={$odbc_prefix} \
            --enable-systemd=no \


          # make install
EOF
        )
        ->withDependentLibraries(
            'openssl',
            'pgsql',
            'spandsp',
            'sofia_sip',
            'libtiff',
            'unixODBC',
            'libjpeg',
            'bzip2',
            'liblzma',
            'libpng',
            'sqlite3',
            'zlib',
            'curl',
            'cares',
            'nghttp2',
            'nghttp3',
            'ngtcp2',
            'brotli',
            'pcre',
            'libopus',
            'speex',
            'speexdsp',
            //'libldns'
            'libyaml',
           // 'portaudio'
        )
    ;

    $p->addLibrary($lib);
};
