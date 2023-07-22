<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $util_linux_prefix = UTIL_LINUX_PREFIX;
    $lib = new Library('util_linux');
    $lib->withHomePage('http://en.wikipedia.org/wiki/Util-linux')
        ->withLicense('https://github.com/util-linux/util-linux/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('http://en.wikipedia.org/wiki/Util-linux')
        ->withManual('http://en.wikipedia.org/wiki/Util-linux/util-linux/tree/v2.39.1/Documentation')
        ->withFile('util-linux-v2.39.1.tar.gz')
        ->withDownloadScript(
            'util-linux',
            <<<EOF
                git clone -b v2.39.1  --depth=1 https://github.com/util-linux/util-linux.git
EOF
        )
        ->withPrefix($util_linux_prefix)
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
