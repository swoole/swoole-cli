<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libeigen_prefix = LIBEIGEN_PREFIX;

    //线性代数的 C++ 模板库：矩阵、向量、数值求解器和相关算法
    //线性运算代数库
    $lib = new Library('libeigen');
    $lib->withHomePage('https://eigen.tuxfamily.org/index.php?title=Main_Page')
        ->withLicense('https://gitlab.com/libeigen/eigen/-/blob/master/COPYING.APACHE', Library::LICENSE_SPEC)
        ->withManual('https://gitlab.com/libeigen/eigen.git')
        ->withManual('https://gitlab.com/libeigen/eigen/-/blob/3.4.0/INSTALL?ref_type=tags')
        ->withFile('eigen-3.4.0.tar.gz')
        ->withDownloadScript(
            'eigen',
            <<<EOF
                git clone -b 3.4.0  --depth=1 https://gitlab.com/libeigen/eigen.git
EOF
        )
        ->withPrefix($libeigen_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libeigen_prefix)
        ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libeigen_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('example')
        ->withBinPath($libeigen_prefix . '/bin/')
       // ->withDependentLibraries('cholmod', 'umfpack', 'klu', 'superlu', 'spqr')

    ;

    $p->addLibrary($lib);
};
