<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'aria2'
    ];
    $ext = (new Extension('aria2'))
        ->withHomePage('https://aria2.github.io/')
        ->withManual('https://aria2.github.io/') //如何选开源许可证？
        ->withLicense('https://www.jingjingxyk.com/LICENSE', Extension::LICENSE_GPL);
    call_user_func_array([$ext, 'depends'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('aria2', function (Preprocessor $p) {

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
