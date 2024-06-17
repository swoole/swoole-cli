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
    $p->withReleaseArchive('coturn', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $coturn_prefix = COTURN_PREFIX;
        $system_arch=$p->getSystemArch();
        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/coturn/bin/
                mkdir -p {$workdir}/bin/coturn/etc/
                cd {$coturn_prefix}/

                cp -rf {$coturn_prefix}/bin/*  {$workdir}/bin/coturn/bin/
                cp -rf {$coturn_prefix}/etc/*  {$workdir}/bin/coturn/etc/

                for f in `ls {$workdir}/bin/coturn/bin/` ; do
                    echo \$f
                    strip {$workdir}/bin/coturn/bin/\$f
                done


                cd {$workdir}/bin/
                COTURN_VERSION=\$({$workdir}/bin/coturn/bin//turnserver --version | tail -n 1)

EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            otool -L {$workdir}/bin/coturn/bin/turnserver
            tar -cJvf {$workdir}/coturn-\${COTURN_VERSION}-macos-{$system_arch}.tar.xz coturn/
            zip -v  {$workdir}/coturn-\${COTURN_VERSION}-macos-{$system_arch}.tar.xz.zip {$workdir}/coturn-\${COTURN_VERSION}-macos-{$system_arch}.tar.xz
EOF;
        } else {
            $cmd .= <<<EOF
            file {$workdir}/bin/coturn/bin/turnserver
            readelf -h {$workdir}/bin/coturn/bin/turnserver
            tar -cJvf {$workdir}/coturn-\${COTURN_VERSION}-linux-{$system_arch}.tar.xz coturn/
            zip -v  {$workdir}/coturn-\${COTURN_VERSION}-linux-{$system_arch}.tar.xz.zip {$workdir}/coturn-\${COTURN_VERSION}-linux-{$system_arch}.tar.xz
EOF;
        }
        return $cmd;
    });
};
