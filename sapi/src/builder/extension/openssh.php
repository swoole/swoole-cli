<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'openssl',
        'zlib',
        'openssh'
    ];
    $ext = (new Extension('openssh'))
        ->withHomePage('https://www.openssh.com/')
        ->withManual('https://developer.baidu.com/article/detail.html?id=293377')
        ->withLicense('https://www.jingjingxyk.com/LICENSE', Extension::LICENSE_SPEC);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('openssh', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$builddir}/openssh/
                cp -f sshd {$workdir}/bin/

EOF;

        return $cmd;
    });
};
