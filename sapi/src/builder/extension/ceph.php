<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'ceph',
    ];
    $ext = (new Extension('ceph'))
                ->withHomePage('https://ceph.io/')
           ->withLicense('https://github.com/ceph/ceph/blob/main/COPYING-LGPL3', Extension::LICENSE_LGPL)
           ->withManual('https://github.com/ceph/ceph');

    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('ceph', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                test -d {$workdir}/bin/ceph-docs && rm -rf {$workdir}/bin/ceph-docs

                cd {$builddir}/ceph/
                cp -rf cephadm {$workdir}/bin/cephadm

                cp -rf build-doc/output {$workdir}/bin/ceph-docs
                cd {$workdir}/bin/
                tar -cJvf {$workdir}/ceph-docs-vlatest.tar.xz ceph-docs

EOF;
        return $cmd;
    });
};
