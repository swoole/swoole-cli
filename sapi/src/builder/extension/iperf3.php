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
    $p->withReleaseArchive('iperf3', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $installdir = $p->getGlobalPrefix();
        $system_arch=$p->getSystemArch();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$installdir}/iperf3/bin/
                cp -f iperf3 {$workdir}/bin/iperf3
                APP_VERSION=\$({$workdir}/bin/iperf3 -v | head -n 1 | awk '{ print $2 }')
EOF;

        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            xattr -cr {$workdir}/bin/iperf3
            otool -L {$workdir}/bin/iperf3
            tar -cJvf {$workdir}/iperf3-\${APP_VERSION}-macos-{$system_arch}.tar.xz aria2c

EOF;
        } else {
            $cmd .= <<<EOF
              file {$workdir}/bin/iperf3
              readelf -h {$workdir}/bin/iperf3
              tar -cJvf {$workdir}/iperf3-\${APP_VERSION}-linux-{$system_arch}.tar.xz aria2c

EOF;
        }
        return $cmd;

        return $cmd;
    });
};
