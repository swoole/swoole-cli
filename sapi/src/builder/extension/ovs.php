<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'ovs'
    ];
    $ext = (new Extension('ovs'))
        ->withHomePage('https://github.com/openvswitch/ovs.git')
        ->withManual('https://github.com/openvswitch/ovs.git')
        ->withLicense('https://github.com/openvswitch/ovs/blob/master/LICENSE', Extension::LICENSE_APACHE2);
    call_user_func_array([$ext, 'depends'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('ovs', function (Preprocessor $p) {

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
