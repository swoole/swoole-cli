<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openblas_prefix = OPENBLAS_PREFIX;

    //   LAPACK 线性数学库
    //   LAPACK（Linear Algebra PACKage）库，是用Fortran语言编写的线性代数计算库，包含线性方程组求解（AX=b）、矩阵分解、矩阵求逆、求矩阵特征值、奇异值等。该库用BLAS库做底层运算，许多高层的数学库都用BLAS和LAPACK做底层。
    //   LAPACK 包含了求解科学与工程计算中最常见的数值线性代数问题 (lapack的安装包已经包含了blas、cblas、lapacke)
    // BLAS（Basic Linear Algebra Subprograms 基础线性代数程序集）
    // blas提供了一些基本的矩阵和向量运算，lapack提供了更丰富的线性方程求解、二次规划、特征值分解等等的运算
    //-lcblas -lrefblas -lm -lgfortran
    // FFTW ( the Faster Fourier Transform in the West) 是一个快速计算离散傅里叶变换的标准C语言程序集

    //开源矩阵计算库
    $lib = new Library('OpenBLAS');
    $lib->withHomePage('http://www.openblas.net/')
        ->withLicense('https://github.com/xianyi/OpenBLAS/blob/develop/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/xianyi/OpenBLAS.git')
        ->withFile('OpenBLAS-v0.3.24.tar.gz')
        ->withDownloadScript(
            'OpenBLAS',
            <<<EOF

                 git clone -b v0.3.24 --depth=1 https://github.com/xianyi/OpenBLAS.git
EOF
        )

        ->withPrefix($openblas_prefix)

        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
        mkdir -p build
        cd build

        cmake .. \
        -DCMAKE_INSTALL_PREFIX={$openblas_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DBUILD_TESTING=OFF


        # -DBUILD_WITHOUT_LAPACK=ON

        cmake --build . --config Release

        cmake --build . --config Release --target install

EOF
        )

        ->withPkgName('openblas')
        //->withDependentLibraries('lapack')

    ;

    $p->addLibrary($lib);

};




