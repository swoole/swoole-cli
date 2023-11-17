<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'cephadm',
    ];
    $ext = (new Extension('cephadm'))
                ->withHomePage('https://ceph.io/')
           ->withLicense('https://github.com/ceph/ceph/blob/main/COPYING-LGPL3', Extension::LICENSE_LGPL)
           ->withManual('https://github.com/ceph/ceph');

    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->withReleaseArchive('cephadm', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/

                cd {$workdir}/bin/
                cp -f {$builddir}/cephadm/cephadm  .

EOF;
        return $cmd;
    });
};
