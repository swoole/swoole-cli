<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openblas_prefix = OPENBLAS_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;

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

        ->withPrefix($example_prefix)
        /*
         //用于调试
         //当 --with-build_type=dev 时 如下2个配置生效


        // 自动清理构建目录
        ->withCleanBuildDirectory()

        // 自动清理安装目录
        ->withCleanPreInstallDirectory($example_prefix)


        //明确申明 不使用构建缓存
        //例子： thirdparty/openssl (每次都解压全新源代码到此目录）
        ->withBuildLibraryCached(false)

       */

        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$example_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON


            # 配置选项例子
            # -DCMAKE_CXX_STANDARD=14
            # -DCMAKE_C_STANDARD=11
            # -DCMAKE_C_COMPILER=clang \
            # -DCMAKE_CXX_COMPILER=clang++ \
            # -DCMAKE_DISABLE_FIND_PACKAGE_libsharpyuv=ON \
            # -DCMAKE_C_FLAGS="-D_POSIX_C_SOURCE=200809L" \
            # -DOpenSSL_ROOT={$openssl_prefix} \
            # 查找PKGCONFIG配置目录多个使用分号隔开
            # -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$openssl_prefix}" \


            # cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )



        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl')


        /*

        //默认不需要此配置
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$example_prefix}/lib/*.so.*
            rm -rf {$example_prefix}/lib/*.so
            rm -rf {$example_prefix}/lib/*.dylib
EOF
        )
        */



    ;

    $p->addLibrary($lib);

};
