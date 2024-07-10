<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $bison_prefix = BISON_PREFIX;
    $p->addLibrary(
        (new Library('bison'))
            ->withHomePage('https://www.gnu.org/software/bison/')
            ->withUrl('https://ftpmirror.gnu.org/gnu/bison/bison-3.8.tar.gz')
            ->withFileHash('md5', 'b9971f4f58690b7737ab7592d5a0a4e0')
            ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
            ->withConfigure(
                "
                    ./configure --help
                    ./configure --prefix={$bison_prefix}
                    "
            )
            ->withBinPath($bison_prefix . '/bin/')
    );
};
