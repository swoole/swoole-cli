<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'proxychains'
    ];

    $ext = (new Extension('proxychains'))
        ->withHomePage('https://github.com/rofl0r/proxychains-ng.git')
        ->withManual('https://github.com/rofl0r/proxychains-ng.git')
        ->withLicense('https://github.com/rofl0r/proxychains-ng/blob/master/COPYING', Extension::LICENSE_GPL);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->withBeforeConfigureScript('proxychains', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $proxychains_prefix = PROXYCHAINS_PREFIX;

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/proxychains
                cd {$proxychains_prefix}/
                cp -rf bin/ {$workdir}/bin/proxychains/
                cp -f {$builddir}/proxychains/src/proxychains.conf {$workdir}/bin/proxychains/
                cd {$workdir}/bin/

EOF;
        $cmd = $cmd . PHP_EOL;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            otool -L {$workdir}/bin/proxychains/bin/proxychains4
            tar -cJvf {$workdir}/proxychains-vlatest-static-macos-x64.tar.xz proxychains
EOF;
        } else {
            $cmd .= <<<EOF
            file {$workdir}/bin/proxychains/bin/proxychains4
            readelf -h {$workdir}/bin/proxychains/bin/proxychains4
            tar -cJvf {$workdir}/proxychains-vlatest-static-linux-x64.tar.xz proxychains
EOF;
        }
        return $cmd;
    });
};

/*
 * 用法：

    ./bin/proxychains4 -q -f proxychains.conf firefox

 */
