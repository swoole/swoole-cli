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
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('ovs', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$builddir}/ovs/Documentation
                cp -rf _build {$workdir}/bin/ovs_docs

EOF;

        return $cmd;
    });
};
