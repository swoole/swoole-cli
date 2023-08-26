<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $util_linux_prefix = UTIL_LINUX_PREFIX;
    $proxy=parse_url($p->getHttpProxy());
    var_dump($proxy);
    $http_proxy_host=$proxy['host'];
    $http_proxy_port=$proxy['port'];

    $lib = new Library('util_linux');
    $lib->withHomePage('http://en.wikipedia.org/wiki/Util-linux')
        ->withLicense('https://github.com/util-linux/util-linux/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('http://en.wikipedia.org/wiki/Util-linux')
        ->withManual('http://en.wikipedia.org/wiki/Util-linux/util-linux/tree/v2.39.1/Documentation')
        ->withFile('util-linux-v2.39.1.tar.gz')
        ->withAutoUpdateFile()
        ->withDownloadScript(
            'util-linux',
            <<<EOF

                # export GIT_TRACE_PACKET=1
                # export GIT_TRACE=1
                # export GIT_CURL_VERBOSE=1

                # git clone -b v2.39.1  --depth=1 https://github.com/util-linux/util-linux.git


                export GIT_PROXY_COMMAND=/tmp/git-proxy;

                cat  > \$GIT_PROXY_COMMAND <<___eof___
#!/bin/bash
# socat - PROXY:{$http_proxy_host}:\$1:\$2,proxyport={$http_proxy_port}
nc -X connect  -x {$http_proxy_host}:{$http_proxy_port} "\\$1" "\\$2"
___eof___
                chmod +x \$GIT_PROXY_COMMAND;


                # git config --global core.gitproxy "{$p->getRootDir()}/bin/runtime/git-proxy"

                git clone -b v2.39.2 --depth=1 git://git.kernel.org/pub/scm/utils/util-linux/util-linux.git

                # git config --global core.gitproxy ""




EOF
        )
        ->withPrefix($util_linux_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($util_linux_prefix)
        ->withBuildLibraryCached(false)
        ->withConfigure(
            <<<EOF
            sh autogen.sh
            ./configure --help
            ./configure \
            --prefix={$util_linux_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --disable-all-programs \
            --enable-libuuid \
            --enable-static-programs=uuidd,uuidgen

EOF
        )

        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
