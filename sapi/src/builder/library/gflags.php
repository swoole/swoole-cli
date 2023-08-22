<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $gflags_prefix = GFLAGS_PREFIX;
    $p->addLibrary(
        (new Library('gflags'))
            ->withHomePage('https://gflags.github.io/gflags/')
            ->withLicense('https://github.com/gflags/gflags/blob/master/COPYING.txt', Library::LICENSE_BSD)
            ->withManual('https://github.com/gflags/gflags/blob/master/INSTALL.md')
            ->withUrl('https://github.com/gflags/gflags/archive/refs/tags/v2.2.2.tar.gz')
            ->withPrefix($gflags_prefix)
            ->withFile('gflags-v2.2.2.tar.gz')
            ->withConfigure(
                <<<EOF
                mkdir -p build
                cd build
                cmake .. \
                -DCMAKE_INSTALL_PREFIX={$gflags_prefix} \
                -DCMAKE_BUILD_TYPE=Release  \
                -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
                -DBUILD_SHARED_LIBS=OFF \
                -DBUILD_STATIC_LIBS=ON \
                -DINSTALL_HEADERS=ON \
                -DBUILD_gflags_LIBS=ON \
                -DBUILD_CONFIG_TESTS=OFF \
                -DBUILD_TESTING=OFF \
                -DBUILD_NC_TESTS=OFF


EOF
            )
            ->withPkgName('gflags')
            ->withBinPath($gflags_prefix . '/bin/')
    );
};
