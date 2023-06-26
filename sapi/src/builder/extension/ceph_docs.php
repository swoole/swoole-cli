<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'ceph',
    ];
    $ext = (new Extension('ceph_docs'))
                ->withHomePage('https://ceph.io/')
           ->withLicense('https://github.com/ceph/ceph/blob/main/COPYING-LGPL3', Extension::LICENSE_LGPL)
           ->withManual('https://github.com/ceph/ceph');

    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('ceph_docs', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$builddir}/ceph/
                cp -rf cephadm {$workdir}/bin/cephadm
                cp -rf build-doc/output {$workdir}/bin/ceph-docs

EOF;
        return $cmd;
    });
};
