<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libgomp_prefix = LIBGOMP_PREFIX;

    # OpenMP（libgomp）
    # 由于 OpenMP 内置于编译器中，因此无需安装外部库即可编译此代码  并行编程


    $lib = new Library('libgomp');
    $lib->withHomePage('https://www.openmp.org/')
         ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
         ->withUrl('')
         ->withManual('https://www.openmp.org/specifications/')
         ->withManual('https://github.com/gcc-mirror/gcc/blob/master/libgomp/libgomp.h')
        ->withHttpProxy(true, true)
        ->withFile('gcc-latest.tar.gz')
        ->withDownloadScript(
            'gcc',
            <<<EOF
            git://gcc.gnu.org/git/gcc.git
EOF
        )
        ->withPrefix($libgomp_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libgomp_prefix)
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
          return 0

EOF
        )

    ;

    $p->addLibrary($lib);
};

/*

       GCC 的 编译器都支持 OpenMP 和 OpenACC。
       -fopenmp  -fopenmp-simd   -fopenacc

        OpenMP  OpenACC  https://gcc.gnu.org/projects/gomp/

 */
