<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencv_prefix = OPENCV_PREFIX;
    $lib = new Library('example');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withManual('https://github.com/opencv/opencv.git')
        ->withDownloadScript(
            'opencv_contrib',
            <<<EOF
                git clone -b 5.x  --depth=1 https://github.com/opencv/opencv_contrib.git
EOF
        )
        ->withSkipDownload()
        ->withUntarArchiveCommand('')
        ->withPrefix($opencv_prefix)
        ->withBuildScript(
            <<<EOF
            apk add python3 py3-pip  ccache
            pip3 install numpy  -i https://pypi.tuna.tsinghua.edu.cn/simple
            test -d opencv_contrib || git clone -b 5.x  https://github.com/opencv/opencv_contrib.git --depth 1 --progress
            test -d opencv || git clone -b 5.x  https://github.com/opencv/opencv.git --depth 1 --progress
EOF
        )
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
