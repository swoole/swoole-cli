<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $sndio_audio_prefix = SNDIO_AUDIO_PREFIX;
    $alsa_audio_prefix = ALSA_AUDIO_PREFIX;
    $lib = new Library('sndio_audio');
    $lib->withHomePage('https://sndio.org/')
        ->withLicense('https://spdx.org/licenses/BSD-3-Clause.html', Library::LICENSE_BSD)
        ->withManual('https://sndio.org/install.html')
        ->withUrl('https://sndio.org/sndio-1.9.0.tar.gz')
        ->withFileHash('sha256','f30826fc9c07e369d3924d5fcedf6a0a53c0df4ae1f5ab50fe9cf280540f699a')
        ->withPrefix($sndio_audio_prefix)
        ->withBuildCached(false)
        ->withBuildCached(false)
        ->withConfigure(
            <<<EOF

        ./configure --help

        # LDFLAGS="\$LDFLAGS -static"

        PACKAGES='libbsd '
        PACKAGES="\$PACKAGES alsa"

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
        ./configure \
        --prefix={$sndio_audio_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --enable-alsa=yes \
        --with-libbsd

EOF
        )
        ->withPkgName('example')
        ->withBinPath($sndio_audio_prefix . '/bin/')
        ->withDependentLibraries(
            'alsa_audio',
            'libbsd'
        )
    ;

    $p->addLibrary($lib);

};

