<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libexpat_prefix = LIBEXPAT_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('libexpat');
    $lib->withHomePage('https://libexpat.github.io/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/libexpat/libexpat/archive/refs/tags/R_2_5_0.tar.gz')
        ->withFile('libexpat-R_2_5_0.tar.gz')
        ->withManual('https://github.com/libexpat/libexpat.git')
        ->withPrefix($libexpat_prefix)
        ->withConfigure(
            <<<EOF
            cd expat
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libexpat_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_POLICY_VERSION_MINIMUM=3.5


EOF
        )
        ->withPkgName('expat')
        ->withBinPath($libexpat_prefix . '/bin/')
    ;

    $p->addLibrary($lib);

};
