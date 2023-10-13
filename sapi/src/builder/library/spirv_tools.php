<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openssl_prefix = SPIRV_TOOLS_PREFIX;
    $lib = new Library('spirv_tools');
    $lib->withHomePage('https://github.com/KhronosGroup/SPIRV-Tools.git')
        ->withLicense('https://github.com/KhronosGroup/SPIRV-Tools/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/KhronosGroup/SPIRV-Tools.git')
        ->withFile('SPIRV-Tools-latest.tar.gz')
        ->withDownloadScript(
            'SPIRV-Tools',
            <<<EOF
                git clone -b main  --depth=1 https://github.com/KhronosGroup/SPIRV-Tools.git
                cd SPIRV-Tools
                python3 utils/git-sync-deps
                cd ..
EOF
        )
        ->withPrefix($openssl_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($openssl_prefix)
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$openssl_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DSPIRV_TOOLS_BUILD_STATIC=ON \
            -DSPIRV_SKIP_TESTS=ON



            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('SPIRV-Tools')
        ->withPkgName('SPIRV-Tools-shared')
        ->withBinPath($openssl_prefix . '/bin/');


    $p->addLibrary($lib);


};
