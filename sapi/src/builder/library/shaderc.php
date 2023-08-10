<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $shaderc_prefix =SHADERC_PREFIX;
    $lib = new Library('shaderc');
    $lib->withHomePage('https://github.com/google/shaderc.git')
        ->withLicense('https://github.com/google/shaderc/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/google/shaderc.git')
        ->withAutoUpdateFile()
        ->withFile('shaderc-latest.tar.gz')
        ->withDownloadScript(
            'shaderc',
            <<<EOF
                git clone -b main  --depth=1 https://github.com/google/shaderc.git
EOF
        )

        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja
EOF
        )
        ->withPrefix($shaderc_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
             -GNinja \
            -DCMAKE_INSTALL_PREFIX={$shaderc_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DSHADERC_SKIP_TESTS=ON  \
            -DSHADERC_SKIP_EXAMPLES=ON
            -DSHADERC_SKIP_COPYRIGHT_CHECK=ON \
            -DENABLE_EXCEPTIONS=ON  \
            -DENABLE_CTEST=OFF \
            -DENABLE_GLSLANG_BINARIES=OFF \
            -DSPIRV_SKIP_EXECUTABLES=ON \
            -DSPIRV_TOOLS_BUILD_STATIC=ON

            ninja -j{$p->getMaxJob()}
            ninja install

EOF
        )

        ->withBinPath($shaderc_prefix . '/bin/')

    ;


    $p->addLibrary($lib);


};
