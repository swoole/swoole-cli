<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opensound_audio_prefix = OPEN_SOUND_AUDIO_PREFIX;
    $lib = new Library('opensound_audio');
    $lib->withHomePage('http://www.opensound.com/oss.html')
        ->withLicense('http://www.4front-tech.com/developer/sources/stable/gpl/oss-v4.2-build2019-src-gpl.txt', Library::LICENSE_GPL)
        ->withManual('http://developer.opensound.com/')
        ->withUrl('http://www.4front-tech.com/developer/sources/stable/gpl/oss-v4.2-build2020-src-gpl.tar.bz2')
        ->withfile('opensound_audio-oss-v4.2-build2020-src-gpl.tar.bz2')
        ->withPrefix($opensound_audio_prefix)
        ->withBuildCached(false)
        ->withBuildCached(false)
        ->withConfigure(
            <<<EOF
        ./configure --help

        ./configure \
        --prefix={$opensound_audio_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --enable-alsa=yes \
         --disable-python

EOF
        )
        ->withDependentLibraries(
            'alsa_audio'
        );

    $p->addLibrary($lib);

};

