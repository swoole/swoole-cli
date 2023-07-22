<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $spandsp_prefix = SPANDSP_PREFIX;
    $lib = new Library('spandsp');
    $lib->withHomePage('https://github.com/freeswitch/spandsp.git')
        ->withLicense('https://github.com/freeswitch/spandsp/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/freeswitch/spandsp.git')
        ->withFile('spandsp-latest.tar.gz')
        ->withDownloadScript(
            'spandsp',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/freeswitch/spandsp.git
EOF
        )
        ->withPrefix($spandsp_prefix)
        ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF


EOF
        )
        ->withDependentLibraries('libtiff', 'libaudiofile');


    $p->addLibrary($lib);
};
