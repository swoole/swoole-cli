<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libarchive_prefix = LIBARCHIVE_PREFIX;
    $p->addLibrary(
        (new Library('libarchive'))
            ->withHomePage('https://github.com/libarchive/libarchive.git')
            ->withManual('https://www.libarchive.org/')
            ->withLicense('https://github.com/libarchive/libarchive/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withUrl('https://github.com/libarchive/libarchive/releases/download/v3.6.2/libarchive-3.6.2.tar.gz')
            ->withPrefix($libarchive_prefix)
            ->withConfigure(
                <<<EOF
                sh build/autogen.sh
                ./configure && make distcheck

                # cmake .
EOF


            )
            ->withBinPath($libarchive_prefix . '/bin/')
    );
};
