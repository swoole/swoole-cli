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
    $p->withReleaseArchive('nginx', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $nginx_prefix = NGINX_PREFIX;

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cp -rf {$nginx_prefix} {$workdir}/bin/
                cd {$workdir}/bin/
                NGINX_VERSION=$(echo $({$workdir}/bin/nginx/sbin/nginx -v 2>&1) | awk -F '/' '{print $2}')

EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
                otool -L {$workdir}/bin/nginx/sbin/nginx
                tar -cJvf {$workdir}/nginx-\${NGINX_VERSION}-macos-x64.tar.xz nginx
EOF;
        } else {
            $cmd .= <<<EOF
                file {$workdir}/bin/nginx/sbin/nginx
                readelf -h {$workdir}/bin/nginx/sbin/nginx
                tar -cJvf {$workdir}/nginx-\${NGINX_VERSION}-linux-x64.tar.xz nginx
EOF;
        }
        return $cmd;
    });
};
