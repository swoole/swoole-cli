<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $suitesparse_prefix = SUITESPARSE_PREFIX;
    $blas_prefix = BLAS_PREFIX;
    $lapack_prefix = LAPACK_PREFIX;
    $gmp_prefix = GMP_PREFIX;
    $mpfr_prefix = MPFR_PREFIX;

    $cmake_options = "-DCMAKE_INSTALL_PREFIX={$suitesparse_prefix} ";
    $cmake_options .="-DCMAKE_BUILD_TYPE=Release ";
    $cmake_options .="-DBUILD_SHARED_LIBS=OFF ";
    $cmake_options .="-DBUILD_STATIC_LIBS=ON ";
    $cmake_options .="-DBLAS_LIBRARIES={$blas_prefix}/lib/ ";
    $cmake_options .="-DLAPACK_LIBRARIES={$lapack_prefix}/lib/ ";
    $cmake_options .="-DCMAKE_PREFIX_PATH='{$gmp_prefix};{$blas_prefix};{$lapack_prefix};{$mpfr_prefix}' ";



    # 稀疏矩阵计算包 cholmod
    //文件名称 和 库名称一致
    $lib = new Library('suitesparse');
    $lib->withHomePage('https://people.engr.tamu.edu/davis/suitesparse.html')
        ->withLicense('https://github.com/DrTimothyAldenDavis/SuiteSparse/blob/dev/LICENSE.txt', Library::LICENSE_SPEC)
        ->withManual('https://github.com/DrTimothyAldenDavis/SuiteSparse.git')

        ->withFile('SuiteSparse-latest.tar.gz')
        ->withDownloadScript(
            'SuiteSparse',
            <<<EOF
                git clone -b dev  --depth=1 https://github.com/DrTimothyAldenDavis/SuiteSparse.git
EOF
        )
        ->withPrefix($suitesparse_prefix)
        ->withPreInstallCommand(
            "alpine",
            <<<EOF
        apk add gfortran libgomp
EOF
        )
        ->withMakeOptions(" CMAKE_OPTIONS='{$cmake_options}' JOBS={$p->getMaxJob()}") # 更多配置查看 makefile
        ->withPkgName('example')
        ->withBinPath($suitesparse_prefix . '/bin/')
         ->withDependentLibraries('blas', 'lapack', 'gmp', 'mpfr')
    ;

    $p->addLibrary($lib);
};
