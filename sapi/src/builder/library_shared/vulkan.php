<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $vulkan_prefix = VULKAN_PREFIX;
    $lib = new Library('vulkan');
    $lib->withHomePage('https://www.khronos.org/vulkan/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://github.com/KhronosGroup/Vulkan-Headers.git')
        ->withFile('Vulkan-Headers-v1.3.257.tar.gz')
        ->withDownloadScript(
            'Vulkan-Headers',
            <<<EOF
                git clone -b v1.3.257  --depth=1 https://github.com/KhronosGroup/Vulkan-Headers.git
EOF
        )
        ->withPrefix($vulkan_prefix)
        ->withBuildCached(false)
        ->withInstallCached(false)
        ->withPreInstallCommand(
            'ubuntu',
            <<<EOF
        apt install zlib1g-dev
EOF
        )

        ->withConfigure(
            <<<EOF
            mkdir -p build
             cd build

             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$vulkan_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=ON  \
            -DBUILD_STATIC_LIBS=OFF

EOF
        )
        ->withBinPath($vulkan_prefix . '/bin/')
    ;
    $p->addLibrary($lib);
};
