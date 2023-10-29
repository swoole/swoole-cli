<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    //as epoll/kqueue/event ports/inotify/eventfd/signalfd support
    $libuv_prefix = LIBUV_PREFIX;
    $p->addLibrary(
        (new Library('libuv'))
            ->withHomePage('https://libuv.org/')
            ->withLicense('https://github.com/libuv/libuv/blob/v1.x/LICENSE', Library::LICENSE_MIT)
            ->withManual('https://github.com/libuv/libuv.git')
            ->withFile('libuv-latest.tar.gz')
            ->withDownloadScript(
                'libuv',
                <<<EOF
             git clone -b v1.x --depth=1 --progress https://github.com/libuv/libuv.git
EOF
            )
            ->withPrefix($libuv_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libuv_prefix)
            ->withConfigure(
                <<<EOF

            sh autogen.sh
            ./configure --help

            ./configure \
            --prefix={$libuv_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
            )
            ->withPkgName('libuv')
    );
};
