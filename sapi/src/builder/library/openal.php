<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openal_prefix = OPENCV_PREFIX;
    $lib = new Library('openal');
    $lib->withHomePage('http://www.openal.org/')
        ->withLicense('http://www.openal.org/platforms/', Library::LICENSE_LGPL)
        ->withUrl('http://www.openal.org/downloads/OpenAL11CoreSDK.zip')
        ->withManual('http://www.openal.org/documentation/')
        ->withUntarArchiveCommand('unzip')
        ->withPrefix($openal_prefix)
        ->withBuildScript(
            <<<EOF

EOF
        )
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
