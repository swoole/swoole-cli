<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;

    $lib = new Library('wasm_micro_runtime');
    $lib->withHomePage('https://bytecodealliance.org/')
        ->withLicense('https://github.com/bytecodealliance/wasm-micro-runtime/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/bytecodealliance/wasm-micro-runtime')
        ->withFile('wasm-micro-runtime-latest.tar.gz')
        ->withDownloadScript(
            'wasm-micro-runtime',
            <<<EOF
                git clone -b main  --depth=1 https://github.com/bytecodealliance/wasm-micro-runtime
EOF
        )
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             # cmake -LAH \$cmake_source_directory/setup.cmake

             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$example_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON

            # cmake --build . --config Release

            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/');

    $p->addLibrary($lib);
};
