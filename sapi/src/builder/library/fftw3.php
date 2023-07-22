<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $fftw3_prefix = FREESWITCH_PREFIX;
    $lib = new Library('fftw3');
    $lib->withHomePage('http://www.fftw.org/')
        ->withLicense('https://github.com/FFTW/fftw3/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('http://www.fftw.org/')
        ->withFile('fftw3-latest.tar.gz')
        ->withDownloadScript(
            'fftw3',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/FFTW/fftw3.git
EOF
        )
        ->withPrefix($fftw3_prefix)
        ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF
            ./configure
             make
             make install
EOF
        )
    ;

    $p->addLibrary($lib);
};
