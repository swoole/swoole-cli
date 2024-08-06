<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $pulse_audio_prefix = PULSE_AUDIO_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $libintl_prefix = LIBINTL_PREFIX;
    $x_libintl_prefix = str_replace('/', '\/', $libintl_prefix);

    $lib = new Library('pulse_audio');
    $lib->withHomePage('https://www.freedesktop.org/wiki/Software/PulseAudio/')
        ->withLicense('http://www.gnu.org/copyleft/lesser.html', Library::LICENSE_GPL)
        ->withManual('https://www.freedesktop.org/wiki/Software/PulseAudio/About/')
        ->withManual('https://gitlab.freedesktop.org/pulseaudio/pulseaudio')
        ->withUrl('https://www.freedesktop.org/software/pulseaudio/releases/pulseaudio-17.0.tar.xz')
        ->withFileHash('sha256', '053794d6671a3e397d849e478a80b82a63cb9d8ca296bd35b73317bb5ceb87b5')
        ->withPrefix($pulse_audio_prefix)
        ->withBuildScript(
            <<<EOF
        meson  -h
        meson setup -h


        sed -i.bak "384 s/'intl'/'intl',dirs: '{$x_libintl_prefix}\/lib'/" meson.build


        CPPFLAGS="-I{$libintl_prefix}/include -I{$libiconv_prefix}/include" \
        LDFLAGS="-L{$libintl_prefix}/lib -L{$libiconv_prefix}/lib" \
        LIBS=" -lintl -liconv " \
        LIBRARY_PATH={$libintl_prefix}/lib \
        meson setup  build \
        -Dprefix={$pulse_audio_prefix} \
        -Dlibdir={$pulse_audio_prefix}/lib \
        -Dincludedir={$pulse_audio_prefix}/include \
        -Dbackend=ninja \
        -Dbuildtype=release \
        -Ddefault_library=static \
        -Db_staticpic=true \
        -Db_pie=true \
        -Dprefer_static=true \
        -Ddaemon=false \
        -Ddoxygen=false \
        -Dman=false \
        -Dtests=false \
        -Dx11=false \
        -Ddbus=false \
        -Dglib=false \
        -Dvalgrind=false

        ninja -C build
        ninja -C build install

EOF
        )
        ->withBinPath($pulse_audio_prefix . '/bin/')
        ->withDependentLibraries(
            'libintl'
        );


    $p->addLibrary($lib);
};
