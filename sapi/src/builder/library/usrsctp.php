<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $usrsctp_prefix = USRSCTP_PREFIX;
    $lib = new Library('usrsctp');
    $lib->withHomePage('https://opus-codec.org/')
        ->withLicense('https://opus-codec.org/license/', Library::LICENSE_SPEC)
        ->withManual('https://opus-codec.org/docs/')
        ->withUrl('https://downloads.xiph.org/releases/opus/opus-1.4.tar.gz')
        ->withPrefix($usrsctp_prefix)
        ->withConfigure(
            <<<EOF

            # sh ./autogen.sh
            ./configure --help
            ./configure \
            --prefix={$usrsctp_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --without-NE10

EOF
        )
        ->withPkgName('opus')
        ->withBinPath($usrsctp_prefix . '/bin/')
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
