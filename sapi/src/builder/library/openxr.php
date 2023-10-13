<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $OpenXR_prefix = OPENXR_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('openxr');
    $lib->withHomePage('https://www.khronos.org/openxr/')
        ->withLicense('https://github.com/KhronosGroup/OpenXR-SDK/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withUrl('https://github.com/KhronosGroup/OpenXR-SDK/archive/refs/tags/release-1.0.28.tar.gz')
        ->withManual('https://github.com/KhronosGroup/OpenXR-SDK.git')
        ->withBuildCached(false)
        ->withPrefix($OpenXR_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($OpenXR_prefix)
        ->withBuildScript(
            <<<EOF
            mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$OpenXR_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DOpenSSL_ROOT={$openssl_prefix} \

            # -DCMAKE_CXX_STANDARD=14
            # -DCMAKE_C_COMPILER=clang \
            # -DCMAKE_CXX_COMPILER=clang++ \
            # -DCMAKE_DISABLE_FIND_PACKAGE_libsharpyuv=ON \

            # -DCMAKE_CXX_STANDARD=14

            # cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('ssl')
        ->withBinPath($OpenXR_prefix . '/bin/')
        ->withDependentLibraries('libpcap', 'openssl');

    $p->addLibrary($lib);


};
