<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $glfw_prefix = GLFW_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('glfw');
    $lib->withHomePage('https://www.glfw.org')
        ->withLicense('https://www.glfw.org/license.html', Library::LICENSE_SPEC)
        ->withManual('https://www.glfw.org/docs/latest/build_guide.html')
        ->withManual('https://www.glfw.org/docs/3.3/build_guide.html#build_link_pkgconfig')

        ->withFile('glfw-latest.tar.gz')
        ->withDownloadScript(
            'glfw',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/glfw/glfw.git
EOF
        )
        ->withPrefix($glfw_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($glfw_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$glfw_prefix} \
            -DCMAKE_C_STANDARD=11 \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DGLFW_BUILD_EXAMPLES=OFF \
            -DGLFW_BUILD_TESTS=OFF \
            -DGLFW_BUILD_DOCS=OFF \
            -DGLFW_INSTALL=ON  \
            -DGLFW_LIBRARY_TYPE=STATIC \
            -DCMAKE_DISABLE_FIND_PACKAGE_X11=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_OpenGL=ON \
            -DGLFW_BUILD_X11=OFF \
            -DCMAKE_C_FLAGS="-D_POSIX_C_SOURCE=200809L"


            cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('glfw3')
        ->withDependentLibraries('glad')
    ;

    $p->addLibrary($lib);

};
