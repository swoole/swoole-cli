<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    # Netlink Protocol Library Suite (libnl)
    $libnl_prefix = LIBNL_PREFIX;
    $lib = new Library('libnl');
    $lib->withHomePage('https://www.infradead.org/~tgr/libnl/')
        ->withLicense('https://github.com/thom311/libnl/blob/main/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/thom311/libnl.git')
        ->withFile('libnl-latest.tar.gz')
        ->withDownloadScript(
            'libnl',
            <<<EOF
                git clone -b main  --depth=1 https://github.com/thom311/libnl.git
EOF
        )
        ->withPrefix($libnl_prefix)
        ->withConfigure(
            <<<EOF
            sh autogen.sh

            ./configure --help

            ./configure \
            --prefix={$libnl_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withPkgName('libnl-3.0')
        ->withPkgName('libnl-genl-3.0')
        ->withPkgName('libnl-idiag-3.0')
        ->withPkgName('libnl-route-3.0')
        ->withPkgName('libnl-xfrm-3.0')
        ->withBinPath($libnl_prefix . '/bin/')
    ;

    $p->addLibrary($lib);
};
