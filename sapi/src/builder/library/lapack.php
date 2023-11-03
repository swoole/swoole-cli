<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $lapack_prefix = LAPACK_PREFIX;
    // Linear Algebra PACKage
    //LAPACK 包含了求解科学与工程计算中最常见的数值线性代数问题 (lapack的安装包已经包含了blas、cblas、lapacke)
    //解决数值线性代数中最常见的问题

    // blas提供了一些基本的矩阵和向量运算
    // lapack提供了更丰富的线性方程求解、二次规划、特征值分解等等的运算。
    // cblas是blas的c接口，lapacke是lapack的c接口

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
            -DCMAKE_INSTALL_LIBDIR={$lapack_prefix}/lib \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DBUILD_INDEX64=ON

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            cp -f {$lapack_prefix}/lib/liblapack64.a {$lapack_prefix}/lib/liblapack.a
EOF
        )
        ->withPkgName('blas64')
        ->withPkgName('lapack64')
        ->withDependentLibraries('blas')
    ;

    $p->addLibrary($lib);
};
