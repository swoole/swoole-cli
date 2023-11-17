<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'ceph_docs',
    ];
    $ext = (new Extension('ceph_docs'))
                ->withHomePage('https://ceph.io/')
           ->withLicense('https://github.com/ceph/ceph/blob/main/COPYING-LGPL3', Extension::LICENSE_LGPL)
           ->withManual('https://github.com/ceph/ceph');

    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->withReleaseArchive('ceph_docs', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();

        $cmd = <<<EOF
                cd {$builddir}/ceph_docs
                mkdir -p {$workdir}/bin/
                test -d {$workdir}/bin/ceph-docs && rm -rf {$workdir}/bin/ceph-docs

                cp -rf build-doc/output {$workdir}/bin/ceph-docs
                cd {$workdir}/bin/
                tar -cJvf {$workdir}/ceph-docs-vlatest.tar.xz ceph-docs

EOF;
        return $cmd;
    });
};
