<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    if ($p->getOsType() == 'macos') {
        $bison_prefix = BISON_PREFIX;
        $p->addLibrary(
            (new Library('bison'))
                ->withHomePage('https://www.gnu.org/software/bison/')
                ->withUrl('http://ftp.gnu.org/gnu/bison/bison-3.8.tar.gz')
                ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
                ->withConfigure(
                    "
                    ./configure --help
                    ./configure --prefix={$bison_prefix}
                    "
                )
                ->withBinPath($bison_prefix . '/bin/')
        );
    }
};
