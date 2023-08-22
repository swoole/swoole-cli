<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libyaml_cpp_prefix = LIBYAML_CPP_PREFIX;
    $lib = new Library('libyaml_cpp');
    $lib->withHomePage('https://github.com/jbeder/yaml-cpp.git')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/jbeder/yaml-cpp.git')
        ->withFile('yaml-cpp-latest.tar.gz')
        ->withDownloadScript(
            'yaml-cpp',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/jbeder/yaml-cpp.git
EOF
        )
        ->withPrefix($libyaml_cpp_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -G "Unix Makefiles" \
            -DCMAKE_INSTALL_PREFIX={$libyaml_cpp_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DYAML_BUILD_SHARED_LIBS=OFF

            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('yaml-cpp.');
    $p->addLibrary($lib);
};
