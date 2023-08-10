<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $speex_prefix = SPEEX_PREFIX;
    $lib = new Library('speex');
    $lib->withHomePage('https://speex.org/')
        ->withLicense('https://github.com/mpruett/audiofile/blob/master/COPYING.GPL', Library::LICENSE_GPL)
        ->withManual('https://speex.org/docs/')
        ->withUrl('http://downloads.xiph.org/releases/speex/speex-1.2.1.tar.gz')
        ->withPrefix($speex_prefix)
        ->withConfigure(
            <<<EOF

            ./configure --help
            ./configure \
            --prefix={$speex_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        ->withPkgName('speex')
        ->withBinPath($speex_prefix . '/bin/')
    ;

    $p->addLibrary($lib);
};
