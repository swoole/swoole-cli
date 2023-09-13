<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $lapack_prefix = LAPACK_PREFIX;
    // Linear Algebra PACKage
    //LAPACK 包含了求解科学与工程计算中最常见的数值线性代数问题 (lapack的安装包已经包含了blas、cblas、lapacke)
    //解决数值线性代数中最常见的问题
    $lib = new Library('lapack');
    $lib->withHomePage('https://www.netlib.org/lapack/')
        ->withLicense('https://www.netlib.org/lapack/LICENSE.txt', Library::LICENSE_SPEC)
        ->withManual('https://github.com/Reference-LAPACK/lapack.git')
        ->withFile('lapack-latest.tar.gz')
        ->withDownloadScript(
            'lapack',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/Reference-LAPACK/lapack.git
EOF
        )
        ->withPrefix($lapack_prefix)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add gfortran
EOF
        )
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$lapack_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_INDEX64=ON

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('blas64')
        ->withPkgName('lapack64');

    $p->addLibrary($lib);
};
