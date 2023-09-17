<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libyuv_prefix = LIBYUV_PREFIX;

    $lib = new Library('libyuv');
    $lib->withHomePage('https://chromium.googlesource.com/libyuv/libyuv')
        ->withLicense('https://chromium.googlesource.com/libyuv/libyuv/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://chromium.googlesource.com/libyuv/libyuv/+/refs/heads/main/docs/getting_started.md')
        ->withFile('libyuv-stable.tar.gz')
        ->withDownloadScript(
            'libyuv',
            <<<EOF
                git clone -b stable  --depth=1 https://chromium.googlesource.com/libyuv/libyuv
EOF
        )
        ->withPrefix($libyuv_prefix)
        ->withBuildScript(
            <<<EOF
        mkdir -p build
        cd build
        cmake .. \
        -DCMAKE_INSTALL_PREFIX={$libyuv_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_POLICY_DEFAULT_CMP0064=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DTEST=OFF \
        -DUNIT_TEST=OFF \
        -DCMAKE_DISABLE_FIND_PACKAGE_JPEG=ON

        cmake --build . --config Release --target install

EOF
        )
        ->withBuildLibraryCached(false)
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$libyuv_prefix}/lib/*.so.*
            rm -rf {$libyuv_prefix}/lib/*.so
            rm -rf {$libyuv_prefix}/lib/*.dylib
            mkdir -p {$libyuv_prefix}/lib/pkgconfig/

            cat > {$libyuv_prefix}/lib/pkgconfig/yuv.pc <<'__libyuv__EOF'
prefix={$libyuv_prefix}
libdir=\${prefix}/lib
includedir=\${prefix}/include

Name: yuv
Description: libyuv library
Version: 0.0.1

Requires:
Libs: -L\${libdir} -lyuv
Cflags: -I\${includedir}

__libyuv__EOF

EOF
        )
        ->withPkgName('yuv')
        ->withBinPath($libyuv_prefix . '/bin/')
    ;

    $p->addLibrary($lib);

};
