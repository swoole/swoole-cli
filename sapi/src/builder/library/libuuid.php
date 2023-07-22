<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libuuid_prefix = OPENCV_PREFIX;
    $lib = new Library('libuuid');
    $lib->withHomePage('https://sourceforge.net/projects/libuuid/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://jaist.dl.sourceforge.net/project/libuuid/libuuid-1.0.3.tar.gz')
        ->withManual('https://github.com/opencv/opencv.git')
        ->withDownloadScript(
            'libuuid',
            <<<EOF
                git clone -b 5.x  --depth=1 https://github.com/opencv/opencv_contrib.git
EOF
        )
        ->withSkipDownload()
        ->withUntarArchiveCommand('')
        ->withPrefix($libuuid_prefix)
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
