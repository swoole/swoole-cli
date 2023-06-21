<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $opencl_prefix = LIBXPM_PREFIX;
    $opencl_prefix = '/usr/opencl';
    $lib = new Library('opencl');
    $lib->withHomePage('https://www.khronos.org/opencl/')
        ->withLicense('https://github.com/KhronosGroup/OpenCL-SDK/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withUrl('git clone --recursive https://github.com/KhronosGroup/OpenCL-SDK.git')
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
EOF
        )
        ->withConfigure(
            <<<EOF
            autoreconf -ivf
            ./configure --help

            LDFLAGS="-static " \
            ./configure --prefix={$opencl_prefix} \
            --enable-legacy \
            --enable-strict-compilation

EOF
        )
        ->withPkgName('opencv');

    $p->addLibrary($lib);
};
