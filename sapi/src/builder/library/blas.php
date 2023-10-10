<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {

    $blas_prefix = BLAS_PREFIX;

    # BLAS（Basic Linear Algebra Subprograms）即基础线性代数子程序库

    $lib = new Library('blas');
    $lib->withHomePage('https://www.netlib.org/blas/')
        ->withLicense('https://www.netlib.org/blas/', Library::LICENSE_SPEC)
        ->withManual('https://www.netlib.org/blas/')
        ->withUrl('http://www.netlib.org/blas/blas-3.11.0.tgz')
        ->withPrefix($blas_prefix)
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$blas_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON

        cmake --build . --config Release

        cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('blas')
    ;

    $p->addLibrary($lib);
};
