<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'coturn'
    ];
    $ext = (new Extension('coturn'))
        ->withHomePage('https://github.com/coturn/coturn/')
        ->withManual('https://github.com/coturn/coturn/tree/master/docs')
        ->withLicense('https://github.com/coturn/coturn/blob/master/LICENSE', Extension::LICENSE_SPEC);
    call_user_func_array([$ext, 'depends'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('coturn', function (Preprocessor $p) {

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
