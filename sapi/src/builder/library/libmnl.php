<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmnl_prefix = LIBMNL_PREFIX;
    $lib = new Library('libmnl');
    $lib->withHomePage('https://www.netfilter.org/projects/libmnl/index.html')
        ->withLicense('http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt', Library::LICENSE_LGPL)
        ->withManual('https://www.netfilter.org/projects/libmnl/index.html')
        ->withFile('libmnl-latest.tar.gz')
        ->withHttpProxy(true, true)
        ->withDownloadScript(
            'libmnl',
            <<<EOF
            git clone -b master  --depth=1 git://git.netfilter.org/libmnl
EOF
        )
        ->withPrefix($libmnl_prefix)
        ->withConfigure(
            <<<EOF
        sh ./autogen.sh

        ./configure --help

        ./configure \
        --prefix={$libmnl_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --without-doxygen

EOF
        )
        ->withPkgName('libmnl');

    $p->addLibrary($lib);
};
