<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;

    //线性代数的 C++ 模板库：矩阵、向量、数值求解器和相关算法
    $lib = new Library('libeigen');
    $lib->withHomePage('https://eigen.tuxfamily.org/index.php?title=Main_Page')
        ->withLicense('https://gitlab.com/libeigen/eigen/-/blob/master/COPYING.APACHE', Library::LICENSE_SPEC)
        ->withManual('https://gitlab.com/libeigen/eigen.git')
        ->withFile('opencv-latest.tar.gz')
        ->withDownloadScript(
            'opencv',
            <<<EOF
                git clone -b main  --depth=1 https://gitlab.com/libeigen/eigen.git
EOF
        )
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)
        ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build

            -DCMAKE_INSTALL_PREFIX={$example_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON

            cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'openssl')

    ;

    $p->addLibrary($lib);
};
