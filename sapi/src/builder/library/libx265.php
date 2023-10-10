<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libx265_prefix = LIBX265_PREFIX;

    $lib = new Library('libx265');
    $lib->withHomePage('https://www.videolan.org/developers/x265.html')
        ->withLicense('https://bitbucket.org/multicoreware/x265_git/src/master/COPYING', Library::LICENSE_LGPL)
        ->withFile('libx265_v3.5.tar.gz')
        ->withDownloadScript(
            'x265_git',
            <<<EOF
            git clone -b 3.5 --progress --depth=1  https://bitbucket.org/multicoreware/x265_git.git
EOF
        )
        ->withManual('https://bitbucket.org/multicoreware/x265_git.git')
        ->withPrefix($libx265_prefix)
        ->withPreInstallCommand(
            'debian',
            <<<EOF
            apt install nasm
EOF
        )
        ->withConfigure(
            <<<EOF
            mkdir -p build-dir
            cd build-dir

            cmake \
            -G"Unix Makefiles" ../source  \
            -DCMAKE_INSTALL_PREFIX={$libx265_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_CURRENT_SOURCE_DIR={$p->getBuildDir()}/libx265/source/ \
            -DCMAKE_C_COMPILER={$p->get_C_COMPILER()} \
            -DCMAKE_CXX_COMPILER={$p->get_CXX_COMPILER()} \
            -DENABLE_SHARED=OFF \
            -DSTATIC_LINK_CRT=ON \
            -DENABLE_LIBNUMA=OFF

            # -DCMAKE_CXX_IMPLICIT_LINK_LIBRARIES=' -lm -lstdc++ '


EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            sed -i.backup "s/-lssp_nonshared//g" {$libx265_prefix}/lib/pkgconfig/x265.pc
            sed -i.backup "s/-lgcc_s//g" {$libx265_prefix}/lib/pkgconfig/x265.pc
            sed -i.backup "s/-lgcc//g" {$libx265_prefix}/lib/pkgconfig/x265.pc
            sed -i.backup "s/-ldl//g" {$libx265_prefix}/lib/pkgconfig/x265.pc
EOF
        )
        ->withPkgName('x265')
        ->withBinPath($libx265_prefix . '/bin/')
        // ->withDependentLibraries('numa')
    ;
    $p->addLibrary($lib);
};
