<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $fftw3_prefix = FFTW3_PREFIX;
    $lib = new Library('fftw3');
    $lib->withHomePage('http://www.fftw.org/')
        ->withLicense('https://github.com/FFTW/fftw3/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/BtbN/FFmpeg-Builds/blob/master/scripts.d/25-fftw3.sh')
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

        ./bootstrap.sh  \
         --enable-maintainer-mode \
        --disable-shared \
        --enable-static \
        --disable-fortran \
        --disable-doc \
        --with-our-malloc \
        --enable-threads \
        --with-combined-threads \
        --with-incoming-stack-boundary=2 \
EOF
        )
    ;

    $p->addLibrary($lib);
};
