<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencl_prefix = OPENCL_PREFIX;
    $lib = new Library('opencl');
    $lib->withHomePage('https://www.khronos.org/opencl/')
        ->withLicense('https://github.com/KhronosGroup/OpenCL-SDK/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/KhronosGroup/OpenCL-SDK.git')
        //->withFile('OpenCL-SDK-v2023.04.17.tar.gz')
        ->withFile('OpenCL-SDK-latest.tar.gz')
        ->withDownloadScript(
            'OpenCL-SDK',
            <<<EOF
        # git clone -b v2023.04.17 --progress  --recursive  https://github.com/KhronosGroup/OpenCL-SDK.git
        git clone -b main --progress  --recursive --depth=1 https://github.com/KhronosGroup/OpenCL-SDK.git
EOF
        )
        ->withPrefix($opencl_prefix)
        ->withCleanPreInstallDirectory($opencl_prefix)
        //->withAutoUpdateFile()
        ->withBuildLibraryCached(false)
        ->withBuildLibraryHttpProxy()
        ->withBuildScript(
            <<<EOF
            mkdir -p build

            cmake  -G "Unix Makefiles" \
            -DCMAKE_BUILD_TYPE=Release  \
            -DCMAKE_INSTALL_PREFIX="{$opencl_prefix}" \
            -DCMAKE_BUILD_TYPE=Release \
            -DOPENCL_SDK_TEST_SAMPLES=OFF \
            -DOPENCL_SDK_BUILD_SAMPLES=OFF \
            -DOPENCL_SDK_BUILD_OPENGL_SAMPLES=OFF \
            -DOPENCL_SDK_TEST_SAMPLES=OFF \
            -B ./build -S . \
            -DCMAKE_DISABLE_FIND_PACKAGE_Doxygen=ON

            cmake --build ./build --target install

EOF
        )
        //默认不需要此配置
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$opencl_prefix}/lib/*.so.*
            rm -rf {$opencl_prefix}/lib/*.so
            rm -rf {$opencl_prefix}/lib/*.dylib
EOF
        )
        ->withPkgName('OpenCL');


    $p->addLibrary($lib);
};

/*
 *  depend library

   https://github.com/KhronosGroup/OpenCL-CLHPP.git

   https://github.com/KhronosGroup/OpenCL-Headers.git

   https://github.com/KhronosGroup/OpenCL-ICD-Loader.git

    https://github.com/ThrowTheSwitch/CMock

    https://github.com/throwtheswitch/cexception.git
    https://github.com/throwtheswitch/unity.git

   # 轻量级跨平台 getopt 替代方案
    https://github.com/likle/cargs

    http://tclap.sourceforge.net/

    https://github.com/nothings/stb




 */
