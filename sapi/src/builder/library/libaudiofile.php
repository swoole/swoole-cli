<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libaudiofile_prefix = OPENCV_PREFIX;
    $lib = new Library('libaudiofile');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withManual('https://github.com/opencv/opencv.git')
        ->withDownloadScript(
            'libaudiofile',
            <<<EOF

EOF
        )
        ->withSkipDownload()
        ->withUntarArchiveCommand('')
        ->withPrefix($libaudiofile_prefix)
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
