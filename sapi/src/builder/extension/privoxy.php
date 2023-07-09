<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'privoxy'
    ];
    $ext = (new Extension('privoxy'))
        ->withHomePage('https://www.privoxy.org')
        ->withManual('https://www.privoxy.org/user-manual/quickstart.html')
        ->withLicense('https://www.privoxy.org/gitweb/?p=privoxy.git;a=blob_plain;f=LICENSE.GPLv3;h=f288702d2fa16d3cdf0035b15a9fcbc552cd88e7;hb=HEAD', Extension::LICENSE_GPL);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('privoxy', function (Preprocessor $p) {
        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $installdir = $p->getGlobalPrefix();
        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$builddir}/privoxy
                cp -rf privoxy {$workdir}/bin/
                cp -rf doc/webserver {$workdir}/bin/privoxy-docs
                cd {$installdir}/privoxy
                mkdir -p {$workdir}/bin/privoxy-conf
                cp -rf etc {$workdir}/bin/privoxy-conf/


EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            otool -L {$workdir}/bin/privoxy
EOF;
        } else {
            $cmd .= <<<EOF
              file {$workdir}/bin/privoxy
              readelf -h {$workdir}/bin/privoxy
EOF;
        }
        return $cmd;
    });
};
