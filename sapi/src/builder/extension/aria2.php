<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'aria2'
    ];
    $ext = (new Extension('aria2'))
        ->withHomePage('https://aria2.github.io/')
        ->withManual('https://aria2.github.io/') //如何选开源许可证？
        ->withLicense('https://www.jingjingxyk.com/LICENSE', Extension::LICENSE_GPL);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->withReleaseArchive('aria2', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $system_arch=$p->getSystemArch();
        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$builddir}/aria2/src
                cp -f aria2c {$workdir}/bin/
                strip {$workdir}/bin/aria2c
                cd {$workdir}/bin/
                APP_VERSION=\$({$workdir}/bin/aria2c -v | head -n 1 | awk '{print $3}')

EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            xattr -cr {$workdir}/bin/aria2c
            otool -L {$workdir}/bin/aria2c
            tar -cJvf {$workdir}/aria2c-\${APP_VERSION}-macos-{$system_arch}.tar.xz aria2c

EOF;
        } else {
            $cmd .= <<<EOF
              file {$workdir}/bin/aria2c
              readelf -h {$workdir}/bin/aria2c
              tar -cJvf {$workdir}/aria2c-\${APP_VERSION}-linux-{$system_arch}.tar.xz aria2c

EOF;
        }
        return $cmd;
    });
};
