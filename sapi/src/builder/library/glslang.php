<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $glslang_prefix = GLSLANG_PREFIX;
    $lib = new Library('glslang');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/KhronosGroup/glslang.git')
        ->withUrl('https://github.com/KhronosGroup/glslang/archive/refs/tags/12.3.1.tar.gz')
        ->withFile('glslang-12.3.1.tar.gz')
        ->withPrefix($glslang_prefix)
        ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$glslang_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DENABLE_CTEST=OFF

            cmake --build . --config Release --target install

EOF
        )

        ->withPkgName('opencv')
        ->withBinPath($glslang_prefix . '/bin/')

    ;


    $p->addLibrary($lib);

};
