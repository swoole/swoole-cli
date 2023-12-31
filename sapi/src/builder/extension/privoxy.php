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
    $p->withReleaseArchive('privoxy', function (Preprocessor $p) {
        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $installdir = $p->getGlobalPrefix();
        $privoxy_prefix = PRIVOXY_PREFIX;

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                test -d {$workdir}/bin/privoxy && rm -rf {$workdir}/bin/privoxy
                cd {$privoxy_prefix}/../
                ls -lh privoxy
                cp -rf privoxy {$workdir}/bin/
                strip {$workdir}/bin/privoxy/sbin/privoxy
                PRIVOXY_VERSION=$({$workdir}/bin/privoxy/sbin/privoxy --help | grep 'Privoxy version' | awk '{print $3}')

EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            otool -L {$workdir}/bin/privoxy/sbin/privoxy
            tar -cJvf {$workdir}/privoxy-\${PRIVOXY_VERSION}-macos-x64.tar.xz privoxy
EOF;
        } else {
            $cmd .= <<<EOF
              file {$workdir}/bin/privoxy/sbin/privoxy
              readelf -h {$workdir}/bin/privoxy/sbin/privoxy
              tar -cJvf {$workdir}/privoxy-\${PRIVOXY_VERSION}-linux-x64.tar.xz privoxy
EOF;
        }
        return $cmd;
    });
};

# TEST
# cd $privoxy_prefix
# ./sbin/privoxy --no-daemon etc/config
