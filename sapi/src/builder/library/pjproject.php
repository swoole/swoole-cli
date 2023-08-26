<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $pjproject_prefix = PJPROJECT_PREFIX;
    $sdl2_prefix = SDL2_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $libopus_prefix = LIBOPUS_PREFIX;
    $libvpx_prefix = LIBVPX_PREFIX;
    $openh264_prefix = OPENH264_PREFIX;
    $ffmpeg_prefix = FFMPEG_PREFIX;
    $upnp_prefix = UPNP_PREFIX;
    $lib = new Library('pjproject');
    $lib->withHomePage('https://www.pjsip.org/')
        ->withLicense('https://github.com/pjsip/pjproject/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('https://github.com/pjsip/pjproject.git')
        ->withFile('pjproject-2.13.1.tar.gz')
        ->withDownloadScript(
            'pjproject',
            <<<EOF
                git clone -b 2.13.1 --depth=1 https://github.com/pjsip/pjproject.git
EOF
        )
        ->withPrefix($pjproject_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($pjproject_prefix)
        ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF

            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"
            PACKAGES="\$PACKAGES speex"
            PACKAGES="\$PACKAGES libsamplerate"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$pjproject_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-epoll \
            --enable-speex-resample \
            --enable-libwebrtc-aec3 \
            --with-sdl={$sdl2_prefix} \
            --with-ffmpeg={$ffmpeg_prefix} \
            --with-openh264={$openh264_prefix} \
            --with-vpx={$libvpx_prefix} \
            --with-ssl={$openssl_prefix} \
            --with-opus={$libopus_prefix} \
            --with-upnp={$upnp_prefix} \
            --without-gnutls

EOF
        )
        ->withBinPath($pjproject_prefix . '/bin/')
        ->withDependentLibraries(
            'openssl',
            'zlib',
            'libsamplerate',
            'speex',
            'sdl2',
            'libopus',
            'libg722',
            'upnp',
            'libvpx',
            'openh264',
            "ffmpeg",
            'sdl2'
           // 'pjsua2'

        )
    ;

    $p->addLibrary($lib);
};
