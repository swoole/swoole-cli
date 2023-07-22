<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libaudiofile_prefix = AUDIOFILE_PREFIX;
    $lib = new Library('libaudiofile');
    $lib->withHomePage('https://audiofile.68k.org/')
        ->withLicense('https://github.com/mpruett/audiofile/blob/master/COPYING.GPL', Library::LICENSE_LGPL)
        ->withManual('https://github.com/mpruett/audiofile.git')
        ->withFile('audiofile-0.3.6.tar.gz')
        ->withDownloadScript(
            'audiofile',
            <<<EOF
            git clone -b audiofile-0.3.6 --depth 1 --progress  https://github.com/mpruett/audiofile.git
EOF
        )
        ->withPrefix($libaudiofile_prefix)
        ->withBuildLibraryCached(false)
        ->withConfigure(
            <<<EOF
            sh autogen.sh
            ./configure --help
            PACKAGES="flac flac++ ogg alsa"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES )" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) -lstdc++ -lm " \
            ./configure \
            --prefix={$libaudiofile_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --disable-examples

EOF
        )
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withDependentLibraries('flac', 'ogg', 'alsa')
        /*
Vorbis
FLAC
Theora
Speex
Icecast
        */
        ;

    $p->addLibrary($lib);
};
