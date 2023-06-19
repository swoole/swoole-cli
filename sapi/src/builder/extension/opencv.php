<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'ffmpeg'
    ];
    $ext = (new Extension('opencv'))
        ->withHomePage('https://opencv.org/')
        ->withManual('https://docs.opencv.org') //如何选开源许可证？
        ->withLicense('https://opencv.org/license/', Extension::LICENSE_APACHE2);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('ffmpeg', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$builddir}/aria2/src
                cp -f aria2c {$workdir}/bin/

EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            otool -L {$workdir}/bin/aria2c
EOF;
        } else {
            $cmd .= <<<EOF
              file {$workdir}/bin/aria2c
              readelf -h {$workdir}/bin/aria2c
EOF;
        }
        return $cmd;
    });
};
