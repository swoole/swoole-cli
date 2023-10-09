<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $abseil_cpp_prefix = ABSL_CPP_PREFIX;
    $lib = new Library('abseil_cpp');
    $lib->withHomePage('https://github.com/abseil/abseil-cpp.git')
        ->withLicense('https://github.com/abseil/abseil-cpp/blob/master/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/abseil/abseil-cpp#build')
        //->withUrl('https://github.com/abseil/abseil-cpp/archive/refs/tags/20230125.3.tar.gz')
        ->withFile('abseil-20230802.1.tar.gz')
        ->withDownloadScript(
            'abseil-cpp',
            <<<EOF
        git clone -b 20230802.1 --depth=1  https://github.com/abseil/abseil-cpp.git
EOF
        )
        ->withPrefix($abseil_cpp_prefix)
        ->withConfigure(
            <<<EOF
            mkdir -p build
            cd build
             cmake ..  \
            -DCMAKE_INSTALL_PREFIX={$abseil_cpp_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DABSL_BUILD_TESTING=OFF \
            -DABSL_USE_GOOGLETEST_HEAD=OFF \
            -DCMAKE_CXX_STANDARD=17 \
            -DABSL_PROPAGATE_CXX_STD=ON

            cmake --build . --target all

EOF
        )
        ->withBinPath($abseil_cpp_prefix . '/bin/')
        ->withPkgName('absl_hash_function_defaults') # 有很多 packages
        ->withPkgName('absl_flat_hash_map')
    ;

    $p->addLibrary($lib);
};
