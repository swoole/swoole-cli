<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libogg_prefix = LIBOGG_PREFIX;
    $lib = new Library('libogg');
    $lib->withHomePage('https://xiph.org/ogg/')
        ->withLicense('https://github.com/mpruett/audiofile/blob/master/COPYING.GPL', Library::LICENSE_LGPL)
        ->withManual('https://xiph.org/ogg/doc/')
        ->withManual('https://xiph.org/downloads/')
        ->withUrl('https://downloads.xiph.org/releases/ogg/libogg-1.3.5.tar.gz')
        ->withPrefix($libogg_prefix)
        ->withConfigure(
            <<<EOF
            ls -lha .

            ./configure --help
            ./configure \
            --prefix={$libogg_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withPkgName('ogg');

    $p->addLibrary($lib);
};
