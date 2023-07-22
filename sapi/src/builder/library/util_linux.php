<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencv_prefix = OPENCV_PREFIX;
    $lib = new Library('util_linux');
    $lib->withHomePage('http://en.wikipedia.org/wiki/Util-linux')
        ->withLicense('https://github.com/util-linux/util-linux/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('http://en.wikipedia.org/wiki/Util-linux')
        ->withManual('http://en.wikipedia.org/wiki/Util-linux/util-linux/tree/v2.39.1/Documentation')
        ->withFile('util-linux-v2.39.1.tar.gz')
        ->withDownloadScript(
            'util-linux',
            <<<EOF
                git clone -b v2.39.1  --depth=1 https://github.com/util-linux/util-linux.git
EOF
        )
        ->withSkipDownload()
        ->withUntarArchiveCommand('')
        ->withPrefix($opencv_prefix)
        ->withBuildScript(
            <<<EOF
            sh autogen.sh
            ./config --help


EOF
        )
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
