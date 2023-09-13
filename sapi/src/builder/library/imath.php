<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $imath_prefix = IMATH_PREFIX;

    $lib = new Library('imath');
    $lib->withHomePage('https://imath.readthedocs.io/en/latest/')
        ->withLicense('https://github.com/AcademySoftwareFoundation/Imath/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withManual('https://github.com/AcademySoftwareFoundation/Imath.git')
        ->withFile('Imath-v3.1.9.tar.gz')
        ->withDownloadScript(
            'Imath',
            <<<EOF
                git clone -b v3.1.9  --depth=1 https://github.com/AcademySoftwareFoundation/Imath.git
EOF
        )
        ->withPrefix($imath_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$imath_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('Imath')
    ;

    $p->addLibrary($lib);
};
