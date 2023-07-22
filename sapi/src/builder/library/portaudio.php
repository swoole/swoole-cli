<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $portaudio_prefix = PORTAUDIO_PREFIX;
    $lib = new Library('portaudio');
    $lib->withHomePage('http://www.portaudio.com/')
        ->withLicense('http://www.portaudio.com/license.html', Library::LICENSE_SPEC)
        ->withManual('https://github.com/PortAudio/portaudio.git')
        ->withUrl('http://files.portaudio.com/archives/pa_stable_v190700_20210406.tgz')
        ->withFile('portaudio-stable_v190700_20210406.tgz')
        ->withPrefix($portaudio_prefix)
        ->withBuildLibraryCached(false)
        ->withConfigure(
            <<<EOF
            autoreconf -if
            ./configure --help
            PACKAGES="alsa"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) " \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) " \
            ./configure \
            --prefix={$portaudio_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-alsa

            # asiosdk2
            # dx7sdk
            # JACK
            # oss
EOF
        )
        ->withDependentLibraries('alsa')

    ;

    $p->addLibrary($lib);
};
