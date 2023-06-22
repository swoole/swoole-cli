<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $freeswitch_prefix = FREESWITCH_PREFIX;
    $lib = new Library('freeswitch');
    $lib->withHomePage('https://github.com/signalwire/freeswitch.git')
        ->withLicense('https://github.com/signalwire/freeswitch/blob/master/LICENSE', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/signalwire/freeswitch/archive/refs/tags/v1.10.9.tar.gz')
        ->withManual('https://freeswitch.com/#getting-started')
        ->withFile('freeswitch-v1.10.9.tar.gz')
        ->withDownloadScript(
            'freeswitch',
            <<<EOF
                git clone -b v1.10.9  --depth=1 https://github.com/signalwire/freeswitch.git
EOF
        )
        ->withPrefix($freeswitch_prefix)
        ->withBuildScript(
            <<<EOF
          ./bootstrap.sh
          ./configure
          make install
EOF
        )
    ;

    $p->addLibrary($lib);
};
