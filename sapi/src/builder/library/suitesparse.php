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
    $cmake_options .= "-DCMAKE_BUILD_TYPE=Release ";
    $cmake_options .= "-DBUILD_SHARED_LIBS=OFF ";
    $cmake_options .= "-DBUILD_STATIC_LIBS=ON ";
    $cmake_options .= "-DBLA_STATIC=ON ";
    $cmake_options .= "-DNSTATIC=OFF ";
    $cmake_options .= "-DDEMO=OFF ";
    $cmake_options .= "-DBLAS_LIBRARIES={$blas_prefix}/lib/ ";
    $cmake_options .= "-DLAPACK_LIBRARIES={$lapack_prefix}/lib/ ";
    $cmake_options .= "-DCMAKE_PREFIX_PATH=\"{$gmp_prefix};{$blas_prefix};{$lapack_prefix};{$mpfr_prefix};\" ";


    # 稀疏矩阵计算包 cholmod

    $lib = new Library("suitesparse");
    $lib->withHomePage('https://people.engr.tamu.edu/davis/suitesparse.html')
        ->withLicense('https://github.com/DrTimothyAldenDavis/SuiteSparse/blob/dev/LICENSE.txt', Library::LICENSE_SPEC)
        ->withManual('https://github.com/DrTimothyAldenDavis/SuiteSparse.git')
        ->withManual('https://github.com/DrTimothyAldenDavis/SuiteSparse/README.md')
        ->withFile('suitesparse-latest.tar.gz')
        ->withDownloadScript(
            'SuiteSparse',
            <<<EOF
            #     git clone -b dev  --depth=1 https://github.com/DrTimothyAldenDavis/SuiteSparse.git
                git clone -b fix_static_build  --depth=1 https://github.com/jingjingxyk/SuiteSparse.git
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
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$suitesparse_prefix}/lib/*.so.*
            rm -rf {$suitesparse_prefix}/lib/*.so
            rm -rf {$suitesparse_prefix}/lib/*.dylib
EOF
        )
        ->withPkgName('SuiteSparse_config')
        ->withPkgName('CHOLMOD')
        //有很多个 pgkconfig 文件，按需设置
        /*
 AMD.pc  CAMD.pc     CHOLMOD.pc  CXSparse.pc     GraphBLAS.pc  KLU_CHOLMOD.pc  Mongoose.pc  SPEX.pc  SuiteSparse_GPURuntime.pc  UMFPACK.pc
 BTF.pc  CCOLAMD.pc  COLAMD.pc   GPUQREngine.pc  KLU.pc        LDL.pc          RBio.pc      SPQR.pc  SuiteSparse_config.pc

         */
        ->withBinPath($suitesparse_prefix . '/bin/')
        ->withDependentLibraries(
            'blas',
            'lapack',
            'gmp',
            'mpfr'
        );

    $p->addLibrary($lib);
};
