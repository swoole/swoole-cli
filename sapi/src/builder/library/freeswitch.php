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
        ->withLicense('https://github.com/signalwire/freeswitch/blob/master/LICENSE', Library::LICENSE_GPL)
        ->withUrl('https://github.com/signalwire/freeswitch/archive/refs/tags/v1.10.9.tar.gz')
        ->withManual('https://freeswitch.com/#getting-started')
        ->withManual('https://developer.signalwire.com/freeswitch/FreeSWITCH-Explained/Installation/Linux/Debian_67240088#about')
        ->withFile('freeswitch-v1.10.9.tar.gz')
        ->withDownloadScript(
            'freeswitch',
            <<<EOF
            git clone -b v1.10.9  --depth=1 https://github.com/signalwire/freeswitch.git
EOF
        )
        ->withPrefix($freeswitch_prefix)
        ->withBuildLibraryCached(false)
        ->withCleanBuildDirectory()
        ->withPreInstallCommand(
            <<<EOF
            apt install -y libtool  libtool-bin yasm uuid-runtime libatomic-ops-dev
            apt install -y uuid-dev
            apt install -y autopoint elfutils
            apt install -y libelf-dev
EOF
        )
        ->withCleanBuildDirectory()
        //->withHttpProxy()
        ->withBuildScript(
            <<<EOF

            # cp -f  build/modules.conf.in bin/modules.conf

            # vi bin/modules.conf
            # 注释如下两行
            # #endpoints/mod_verto
            # #applications/mod_signalwire

            ./bootstrap.sh
            # sed -i.backup "s@^endpoints/mod_verto@#endpoints/mod_verto$@"  modules.conf

            # sed 行注释 参考： https://blog.csdn.net/qq_39677803/article/details/121899559

            sed -i.backup "99 s/^/#&/"  modules.conf
            sed -i.backup "42 s/^/#&/"  modules.conf

            # cp -f {$p->getWorkDir()}/bin/modules.conf modules.conf
            #

            ./configure --help

            PACKAGES="openssl libpq spandsp sofia-sip-ua odbc libjpeg libturbojpeg liblzma libpng sqlite3 zlib libcurl"
            PACKAGES="\$PACKAGES libcares  libbrotlicommon libbrotlidec libbrotlienc"
            PACKAGES="\$PACKAGES libnghttp2 libnghttp3 "
            PACKAGES="\$PACKAGES libngtcp2 libngtcp2_crypto_openssl "
            PACKAGES="\$PACKAGES libpcre  libpcre16  libpcre32  libpcrecpp  libpcreposix "
            PACKAGES="\$PACKAGES speex speexdsp "
            PACKAGES="\$PACKAGES yaml-0.1 "
            PACKAGES="\$PACKAGES ldns "
            PACKAGES="\$PACKAGES ImageMagick  ImageMagick-7.Q16HDRI  MagickCore  MagickCore-7.Q16HDRI  MagickWand MagickWand-7.Q16HDRI  Magick++ Magick++-7.Q16HDRI "
            PACKAGES="\$PACKAGES libedit"
            # PACKAGES="\$PACKAGES uuid"
            PACKAGES="\$PACKAGES libavcodec  libavdevice  libavfilter  libavformat  libavutil  libpostproc  libswresample  libswscale"
            PACKAGES="\$PACKAGES opencv5"
            PACKAGES="\$PACKAGES gmp"
            PACKAGES="\$PACKAGES librabbitmq"
            PACKAGES="\$PACKAGES hiredis"

            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) -I{$libtiff_prefix}/include -I{$bzip2_prefix}/include" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) -L{$libtiff_prefix}/lib -L{$bzip2_prefix}/lib -static " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES )" \
            CFLAGS="-O3  -g  -fms-extensions -std=c11 -Werror,-Wc11-extensions" \
            ./configure \
            --prefix={$freeswitch_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-optimization \
            --with-openssl \
            --with-python3 \
            --with-odbc={$odbc_prefix} \
            --enable-systemd=no \
            --enable-core-pgsql-support


           CFLAGS="-O3  -g  -fms-extensions -std=c11 "   make -j {$p->maxJob}
            make install

            # # Install audio files:
            # make cd-sounds-install cd-moh-install

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
            'libldns',
            'libyaml',
            'imagemagick',
            'libedit',
            'ffmpeg',
            'opencv',
            'gmp',
            'rabbitmq_c',
            'hiredis',

            //'libuuid',
            // 'portaudio'
            // "opencv",
            // "ffmpeg"
            // "libks"
        )
    ;

    $p->addLibrary($lib);
};
