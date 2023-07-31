<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libfvad_prefix = OPENCV_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('libfvad');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/opencv/opencv/archive/refs/tags/4.7.0.tar.gz')
        ->withManual('https://github.com/dpirch/libfvad.git')
        ->withDownloadScript(
            'opencv_contrib',
            <<<EOF
                git clone -b 5.x  --depth=1 https://github.com/dpirch/libfvad.git
EOF
        )
        ->withBuildLibraryCached(false)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            # apk add uuid-runtime
EOF
        )
        ->withPrefix($libfvad_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libfvad_prefix)
        ->withBuildScript(
            <<<EOF
            mkdir -p build
             cd build
             # cmake 查看选项
             # cmake -LH ..
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libfvad_prefix} \
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
        ->withConfigure(
            <<<EOF
            libtoolize -ci
            autoreconf -fi
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$libfvad_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withPkgName('ssl')
        ->withBinPath($libfvad_prefix . '/bin/')
        ->withDependentLibraries('openssl');

    $p->addLibrary($lib);

};
