<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $pcsc_prefix = OPENCV_PREFIX;
    $lib = new Library('PCSC');
    $lib->withHomePage('https://pcsclite.apdu.fr/')
        ->withLicense('https://pcsclite.apdu.fr/', Library::LICENSE_BSD)
        ->withFile('PCSC-latest.tar.gz')
        ->withManual('https://pcsclite.apdu.fr/')
        ->withDownloadScript(
            'PCSC',
            <<<EOF
                git clone --depth=1 https://salsa.debian.org/rousseau/PCSC.git
EOF
        )
        ->withPrefix($pcsc_prefix)
        ->withBuildScript(
            <<<EOF
            ./bootstrap
            ./configure
            make
EOF
        )
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
