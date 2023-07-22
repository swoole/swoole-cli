<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libatomic_prefix = LIBATOMIC_PREFIX;
    $lib = new Library('libatomic');
    $lib->withHomePage('https://github.com/gcc-mirror/gcc.git')
        ->withLicense('https://github.com/gcc-mirror/gcc/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('https://github.com/gcc-mirror/gcc.git')
        ->withManual('http://en.wikipedia.org/wiki/Util-linux/util-linux/tree/v2.39.1/Documentation')
        ->withFile('gcc-13.1.0.tar.gz')
        ->withDownloadScript(
            'gcc',
            <<<EOF
                git clone -b releases/gcc-13.1.0  --depth=1  https://gcc.gnu.org/git/gcc.git
EOF
        )
        ->withPrefix($libatomic_prefix)
        ->withBuildLibraryCached(false)
        ->withConfigure(
            <<<EOF
            cd libatomic
            ./configure --help
            ./configure \
            --prefix={$libatomic_prefix} \
            --enable-shared=no \
            --enable-static=yes


EOF
        )

        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};
