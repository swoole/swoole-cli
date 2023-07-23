<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libudev_prefix = LIBUDEV_PREFIX;
    $lib = new Library('libudev');
    $lib->withHomePage('https://github.com/systemd/systemd.git')
        ->withLicense('https://github.com/systemd/systemd/blob/main/LICENSE.GPL2', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/systemd/systemd/archive/refs/tags/v254-rc1.tar.gz')
        ->withManual('https://github.com/systemd/systemd.git')
        ->withFile('libudev-v254-rc1.tar.gz')
        ->withPrefix($libudev_prefix)
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
