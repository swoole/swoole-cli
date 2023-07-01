<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'iperf3'
    ];
    $ext = (new Extension('iperf3'))
        ->withHomePage('https://www.jingjingxyk.com')
        ->withManual('https://developer.baidu.com/article/detail.html?id=293377')
        ->withLicense('https://www.jingjingxyk.com/LICENSE', Extension::LICENSE_SPEC);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('iperf3', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $installdir = $p->getGlobalPrefix();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$installdir}/iperf3/bin/
                cp -f iperf3 {$workdir}/bin/iperf3
EOF;

        return $cmd;
    });
};
