<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $speexdsp_prefix = SPEEXDSP_PREFIX;
    $lib = new Library('speexdsp');
    $lib->withHomePage('https://speex.org/')
        ->withLicense('https://github.com/mpruett/audiofile/blob/master/COPYING.GPL', Library::LICENSE_GPL)
        ->withManual('https://speex.org/docs/')
        ->withUrl('http://downloads.xiph.org/releases/speex/speexdsp-1.2.1.tar.gz')
        ->withPrefix($speexdsp_prefix)
        ->withConfigure(
            <<<EOF

            ./configure --help
            ./configure \
            --prefix={$speexdsp_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        ->withPkgName('speexdsp')
        ->withBinPath($speexdsp_prefix . '/bin/')
    ;


    $p->addLibrary($lib);
};
