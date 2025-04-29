<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $mimalloc_prefix = MIMALLOC_PREFIX;
    $cmake_options = '';
    if ($p->isLinux()) {
        $cmake_options .= ' -DMI_LIBC_MUSL=ON ';
    }
    $tag = 'v2.2.2';
    $p->addLibrary(
        (new Library('mimalloc'))
            ->withLicense('https://github.com/microsoft/mimalloc/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://microsoft.github.io/mimalloc/')
            ->withUrl('https://github.com/microsoft/mimalloc/archive/refs/tags/' . $tag . '.tar.gz')
            ->withFile('mimalloc-' . $tag . '.tar.gz')
            ->withPrefix($mimalloc_prefix)
            ->withBuildScript(<<<EOF
             # v3.0.3 版本 需要 patch
             # patch 参考
             # https://github.com/microsoft/mimalloc/commit/ccda6b576e3252ebcd1834cbe2585bb354f18141
             # https://github.com/microsoft/mimalloc/issues/1056
             # 清空 34-37 行内容
             # sed -i.bak -e '34s/^.*$//' -e '35s/^.*$//' -e '36s/^.*$//' -e '37s/^.*$//' src/prim/unix/prim.c
             # 替换 34 行内容
             # sed -i.bak  '34s/^.*$/  #include <sys\/prctl\.h>/'  src/prim/unix/prim.c

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
            -DMI_SKIP_COLLECT_ON_EXIT=ON \
            -DMI_OVERRIDE=ON \
            {$cmake_options}

            cmake --build . --config Release

            cmake --build . --config Release --target install
EOF
            )
            ->withPkgName('mimalloc')
    );
};
