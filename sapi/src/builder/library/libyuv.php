<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libyuv_prefix = LIBYUV_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $lib = new Library('libyuv');
    $lib->withHomePage('https://chromium.googlesource.com/libyuv/libyuv')
        ->withLicense('https://chromium.googlesource.com/libyuv/libyuv/+/refs/heads/main/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://chromium.googlesource.com/libyuv/libyuv')
        ->withUrl('https://chromium.googlesource.com/libyuv/libyuv/+archive/refs/heads/main.tar.gz')
        ->withFile('libyuv-main.tar.gz')
        ->withPrefix($libyuv_prefix)
        ->withUntarArchiveCommand('tar-default')
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF

        # sed -i.backup 's/^pattern/;\1/' file.txt
        # 注释匹配行
        sed -i.backup 's/^add_library( \${ly_lib_shared} SHARED \${ly_lib_parts})/# \1/' CMakeLists.txt
        sed -i.backup 's/^  target_link_libraries( \${ly_lib_shared} \${JPEG_LIBRARY} )/# \1/' CMakeLists.txt
        sed -i.backup 's/^set_target_properties( \${ly_lib_shared} PROPERTIES/# \1/' CMakeLists.txt
        sed -i.backup 's/^set_target_properties( \${ly_lib_shared} PROPERTIES/# \1/' CMakeLists.txt
        sed -i.backup 's/^install ( TARGETS \${ly_lib_shared} LIBRARY/# \1/' CMakeLists.txt
        mkdir -p build
        cd build

        cmake -S .. -B . \
        -DCMAKE_INSTALL_PREFIX={$libyuv_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DCMAKE_PREFIX_PATH="{$libjpeg_prefix}" \

        cmake --build . --config Release

        cmake --build . --config Release --target install


EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$libyuv_prefix}/lib/*.so.*
            rm -rf {$libyuv_prefix}/lib/*.so
            rm -rf {$libyuv_prefix}/lib/*.dylib
EOF
        )
        ->withBinPath($libyuv_prefix . '/bin/')
        ->withDependentLibraries('libjpeg');
    $p->addLibrary($lib);
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libyuv_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libyuv_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lyuv');
};
