<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'ovn'
    ];
    $ext = (new Extension('ovn'))
        ->withHomePage('https://github.com/ovn-org/ovn.git')
        ->withManual('https://github.com/ovn-org/ovn.git') //如何选开源许可证？
        ->withLicense('https://www.jingjingxyk.com/LICENSE', Extension::LICENSE_GPL)
        ->withDependentExtensions('ovs')
    ;
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('ovn', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $ovn_prefix = OVN_PREFIX;
        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                test -d {$workdir}/bin/ovn_docs/ && rm -rf {$workdir}/bin/ovn_docs/

                cd {$builddir}/ovn/Documentation
                cp -rf _build {$workdir}/bin/ovn_docs
                cd {$builddir}/ovn
                cp -rf dist-docs {$workdir}/bin/ovn_docs

                # cd $ovn_prefix/../
                # tar -cJvf ovn-vlatest-static-linux-x64.tar.xz ovn
                # cp -f ovn-vlatest-static-linux-x64.tar.xz {$workdir}/bin/
EOF;

        return $cmd;
    });
};
