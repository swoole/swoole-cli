<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libopusenc_prefix = LIBOPUSENC_PREFIX;
    $lib = new Library('libopusenc');
    $lib->withHomePage('https://opus-codec.org/')
        ->withLicense('https://opus-codec.org/license/', Library::LICENSE_SPEC)
        ->withManual('https://opus-codec.org/docs/')
        ->withUrl('https://archive.mozilla.org/pub/opus/libopusenc-0.2.1.tar.gz')
        ->withPrefix($libopusenc_prefix)
        ->withConfigure(
            <<<EOF

            # sh ./autogen.sh
            ./configure --help
            ./configure \
            --prefix={$libopusenc_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --without-NE10

EOF
        )
        ->withPkgName('opus')
        ->withBinPath($libopus_prefix . '/bin/')
        //  ->withDependentLibraries('libne10')  https://github.com/projectNe10/Ne10/blob/master/doc/building.md
    ;


    $p->addLibrary($lib);
    /*
        Vorbis
        FLAC
        Theora
        Speex
        Icecast
    */
};
