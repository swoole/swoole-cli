<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'coturn'
    ];
    $ext = (new Extension('coturn'))
        ->withHomePage('https://github.com/coturn/coturn/')
        ->withManual('https://github.com/coturn/coturn/tree/master/docs')
        ->withLicense('https://github.com/coturn/coturn/blob/master/LICENSE', Extension::LICENSE_SPEC);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('coturn', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $coturn_prefix = COTURN_PREFIX;
        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/coturn/bin/
                mkdir -p {$workdir}/bin/coturn/etc/
                cd {$coturn_prefix}/

                cp -rf {$coturn_prefix}/bin/  {$workdir}/bin/coturn/bin/
                cp -rf {$coturn_prefix}/etc/*  {$workdir}/bin/coturn/etc/
                cd {$workdir}/bin/



EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            otool -L {$workdir}/bin/coturn/bin/turnserver
            tar -cJvf coturn-vlatest-static-macos-x64.tar.xz coturn/
            zip -v  coturn-vlatest-static-macos-x64.tar.xz.zip coturn-vlatest-static-macos-x64.tar.xz
EOF;
        } else {
            $cmd .= <<<EOF
            file {$workdir}/bin/coturn/bin/turnserver
            readelf -h {$workdir}/bin/coturn/bin/turnserver
            tar -cJvf coturn-vlatest-static-linux-x64.tar.xz coturn/
            zip -v  coturn-vlatest-static-linux-x64.tar.xz.zip coturn-vlatest-static-linux-x64.tar.xz
EOF;
        }
        return $cmd;
    });
};
