<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $jack_audio_prefix = JACK_AUDIO_PREFIX;
    $alsa_audio_prefix = ALSA_AUDIO_PREFIX;
    $lib = new Library('jack_audio');
    $lib->withHomePage('https://jackaudio.org/')
        ->withLicense('https://spdx.org/licenses/BSD-3-Clause.html', Library::LICENSE_BSD)
        ->withManual('https://github.com/jackaudio/jack2.git')
        ->withDocumentation('https://github.com/jackaudio/jackaudio.github.com/wiki')
        ->withDocumentation('https://wiki.archlinux.org/title/JACK_Audio_Connection_Kit')
        ->withUrl('https://github.com/jackaudio/jack2/archive/v1.9.22.tar.gz')
        ->withfile('jackaudio-v1.9.22.tar.gz')
        ->withPrefix($jack_audio_prefix)
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
        --prefix={$jack_audio_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --enable-alsa=yes \
        --with-libbsd

EOF
        )
        ->withPkgName('example')
        ->withBinPath($jack_audio_prefix . '/bin/')
        ->withDependentLibraries(
            'alsa_audio',
            'libbsd'
        )
    ;

    $p->addLibrary($lib);

};

