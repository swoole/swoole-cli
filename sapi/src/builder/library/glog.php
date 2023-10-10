<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $glog_prefix = GLOG_PREFIX;
    $gflags_prefix = GFLAGS_PREFIX;
    $libunwind_prefix = LIBUNWIND_PREFIX;
    $lib = new Library('glog');
    $lib->withHomePage('https://github.com/google/glog')
        ->withLicense('https://github.com/google/glog/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withManual('https://github.com/google/glog.git')
        ->withFile('glog-latest.tar.gz')
        ->withDownloadScript(
            'glog',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/google/glog.git
EOF
        )
        ->withPrefix($glog_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -G "Unix Makefiles" \
            -DCMAKE_INSTALL_PREFIX={$glog_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DWITH_GTEST=OFF \
            -DWITH_PKGCONFIG=ON \
            -DWITH_TLS=ON \
            -DWITH_UNWIND=OFF \
            -DWITH_GMOCK=OFF  \
            -DWITH_GFLAGS=ON \
            -DCMAKE_PREFIX_PATH="{$gflags_prefix}"



            cmake --build . --config Release --target install
EOF
        )
        ->withPkgName('libglog')
        ->withDependentLibraries('gflags') //'libunwind',
    ;

    $p->addLibrary($lib);
};
