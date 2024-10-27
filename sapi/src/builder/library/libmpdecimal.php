<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmpdecimal_prefix = LIBMPDECIMAL_PREFIX;
    $lib = new Library('libmpdecimal');
    $lib->withHomePage('https://www.bytereef.org/mpdecimal/')
        ->withLicense('https://www.bytereef.org/mpdecimal/download.html', Library::LICENSE_BSD)
        ->withManual('https://www.bytereef.org/mpdecimal/quickstart.html')
        ->withUrl('https://www.bytereef.org/software/mpdecimal/releases/mpdecimal-4.0.0.tar.gz')
        ->withFileHash('sha256', '942445c3245b22730fd41a67a7c5c231d11cb1b9936b9c0f76334fb7d0b4468c')
        ->withPrefix($libmpdecimal_prefix)
        ->withBuildCached(false)
        ->withConfigure(
            <<<EOF
        ./configure --help

        ./configure \
        --prefix={$libmpdecimal_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --enable-pc=yes \
        --enable-doc=no \
        MACHINE=x64

EOF
        )
        ->withPkgName('libmpdec')
        ->withPkgName('libmpdec++');

    $p->addLibrary($lib);
};
