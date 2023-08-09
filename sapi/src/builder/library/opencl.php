<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencl_prefix = OPENCL_PREFIX;
    $lib = new Library('opencl');
    $lib->withHomePage('https://www.khronos.org/opencl/')
        ->withLicense('https://github.com/KhronosGroup/OpenCL-SDK/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withFile('OpenCL-SDK-v2023.04.17.tar.gz')
        ->withDownloadScript(
            'OpenCL-SDK',
            <<<EOF
        git clone -b v2023.04.17 --progress  --recursive  https://github.com/KhronosGroup/OpenCL-SDK.git
EOF
        )
        ->withPrefix($opencl_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($opencl_prefix)
        ->withBuildScript(
            <<<EOF
      cmake -A x64 \
      -D BUILD_TESTING=OFF \
      -D BUILD_DOCS=OFF \
      -D BUILD_EXAMPLES=OFF \
      -D BUILD_TESTS=OFF \
      -D OPENCL_SDK_BUILD_SAMPLES=ON \
      -D OPENCL_SDK_TEST_SAMPLES=OFF \
      -D CMAKE_TOOLCHAIN_FILE=/vcpkg/install/root/scripts/buildsystems/vcpkg.cmake  \
      -D VCPKG_TARGET_TRIPLET=x64-windows  \
      -B ./OpenCL-SDK/build -S ./OpenCL-SDK
      cmake --build ./OpenCL-SDK/build --target install

      https://github.com/KhronosGroup/OpenCL-Headers.git
      https://github.com/KhronosGroup/OpenCL-ICD-Loader.git
      cmake  \
        -DCMAKE_BUILD_TYPE=Release  \
        -DCMAKE_INSTALL_PREFIX="{$opencl_prefix}" \
        -DOPENCL_ICD_LOADER_HEADERS_DIR="$FFBUILD_PREFIX"/include  \
        -DOPENCL_ICD_LOADER_BUILD_SHARED_LIBS=OFF \
        -DOPENCL_ICD_LOADER_DISABLE_OPENCLON12=ON  \
        -DOPENCL_ICD_LOADER_PIC=ON \
        -DOPENCL_ICD_LOADER_BUILD_TESTING=OFF \
        -DBUILD_TESTING=OFF ..
EOF
        );


    $p->addLibrary($lib);
};
