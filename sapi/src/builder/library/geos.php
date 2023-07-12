<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $geos_prefix = GEOS_PREFIX;
    $p->addLibrary(
        (new Library('geos'))
            ->withHomePage('libgeos.org')
            ->withLicense('https://github.com/libgeos/geos/blob/main/COPYING', Library::LICENSE_LGPL)
            ->withManual('https://github.com/libgeos/geos/blob/main/INSTALL.md')
            ->withPrefix($geos_prefix)
            ->withFile('geos-3.11.2.tar.gz')
            ->withDownloadScript(
                'geos',
                <<<EOF
            git clone -b 3.11.2  https://github.com/libgeos/geos.git
EOF
            )
            ->withBuildScript(
                <<<EOF
                mkdir -p build
                cd build
                cmake .. \
                -DCMAKE_INSTALL_PREFIX={$geos_prefix} \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_STATIC_LIBS=ON \
                -DBUILD_SHARED_LIBS=OFF \
                -DBUILD_TESTING=OFF

                cmake --build . --target install

EOF
            )
            ->withBinPath($geos_prefix . '/bin/')
            ->withPkgName('geos')
    );
};
