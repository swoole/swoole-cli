<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libconfig_prefix = LIBCONFIG_PREFIX;
    $lib = new Library('libconfig');
    $lib->withHomePage('https://hyperrealm.github.io/libconfig/')
        ->withLicense('https://github.com/PJK/libcbor/blob/master/LICENSE.md', Library::LICENSE_MIT)
        ->withManual('https://hyperrealm.github.io/libconfig/')
        ->withManual('https://github.com/hyperrealm/libconfig/blob/master/INSTALL')
        ->withUrl('https://hyperrealm.github.io/libconfig/dist/libconfig-1.7.3.tar.gz')
        ->withPrefix($libconfig_prefix)
        ->withConfigure(
            <<<EOF
            ./configure --help
            ./configure --prefix={$libconfig_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --disable-examples \
            --disable-tests

EOF
        )
        ->withPkgName('libconfig')
        ->withPkgName('libconfig++')
    ;

    $p->addLibrary($lib);
};
