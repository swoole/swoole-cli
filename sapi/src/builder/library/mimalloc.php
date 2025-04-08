<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $mimalloc_prefix = MIMALLOC_PREFIX;
    $p->addLibrary(
        (new Library('mimalloc'))
            ->withLicense('https://github.com/microsoft/mimalloc/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://microsoft.github.io/mimalloc/')
            ->withUrl('https://github.com/microsoft/mimalloc/archive/refs/tags/v3.0.3.tar.gz')
            ->withFile('mimalloc-v3.0.3.tar.gz')
            ->withPrefix($mimalloc_prefix)
            ->withBuildScript(<<<EOF
             mkdir -p build
             cd build
             cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$mimalloc_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DMI_BUILD_SHARED=OFF \
            -DMI_BUILD_STATIC=ON \
            -DMI_BUILD_TESTS=OFF \
            -DMI_INSTALL_TOPLEVEL=ON \
            -DMI_PADDING=OFF \
            -DMI_SKIP_COLLECT_ON_EXIT=ON

            cmake --build . --config Release

            cmake --build . --config Release --target install
EOF
            )
            ->withPkgName('mimalloc')
    );
};
