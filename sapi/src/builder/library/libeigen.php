<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libeigen_prefix = LIBEIGEN_PREFIX;
    $suitesparse_prefix = SUITESPARSE_PREFIX;
    $boost_prefix = BOOST_PREFIX;
    $fftw3_prefix = FFTW3_PREFIX;
    $mpfr_prefix = MPFR_PREFIX;

    //线性代数的 C++ 模板库：矩阵、向量、数值求解器和相关算法
    //线性运算代数库
    $lib = new Library('libeigen');
    $lib->withHomePage('https://eigen.tuxfamily.org/index.php?title=Main_Page')
        ->withLicense('https://gitlab.com/libeigen/eigen/-/blob/master/COPYING.APACHE', Library::LICENSE_SPEC)
        ->withManual('https://gitlab.com/libeigen/eigen.git')
        ->withManual('https://gitlab.com/libeigen/eigen/-/blob/3.4.0/INSTALL?ref_type=tags')
        ->withFile('eigen-latest.tar.gz')
        ->withDownloadScript(
            'eigen',
            <<<EOF
                git clone -b master  --depth=1 https://gitlab.com/libeigen/eigen.git
EOF
        )
        ->withPrefix($libeigen_prefix)
        ->withPreInstallCommand(
            "alpine",
            <<<EOF
            apk add gfortran
            apk add hwloc
EOF
        )
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libeigen_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_PREFIX_PATH="{$suitesparse_prefix};{$boost_prefix};{$fftw3_prefix};{$mpfr_prefix}"

            # -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            # -DFFTW_ROOT={$fftw3_prefix} \

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('example')
        ->withBinPath($libeigen_prefix . '/bin/')
       ->withDependentLibraries(
           'suitesparse',
           'fftw3', //快速傅立叶变换库
           'boost',
           'mpfr'
       )

    ;

    $p->addLibrary($lib);
};

/*
 *  MPFR   高精度运算库
 *  Adolc  自动微分库
 *  fftw   傅立叶变幻
  SuperLU,  PaStiX,  METIS,  Qt4 support,  GoogleHash,  Adolc,  MPFR C++,  fftw,  OpenGL

 SYCL是OpenCL的高级编程模型

  LU分解(LU Factorization)是矩阵分解的一种
  SuperLU https://portal.nersc.gov/project/sparse/superlu/
*/
