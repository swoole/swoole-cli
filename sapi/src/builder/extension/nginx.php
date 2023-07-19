<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'nginx'
    ];
    $ext = (new Extension('nginx'))
        ->withHomePage('https://nginx.org/')
        ->withManual('http://nginx.org/en/docs/configure.html') //如何选开源许可证？
        ->withLicense('https://github.com/nginx/nginx/blob/master/docs/text/LICENSE', Extension::LICENSE_SPEC);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('nginx', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();


        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$builddir}/nginx/objs
                cp -f nginx {$workdir}/bin/

EOF;

        return $cmd;
    });
};
