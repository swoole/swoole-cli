<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libx265_prefix = LIBX265_PREFIX;
    $options = $p->getOsType() == 'macos' ? "" : ' -DSTATIC_LINK_CRT=ON ';

    $lib = new Library('libx265');
    $lib->withHomePage('https://www.videolan.org/developers/x265.html')
        ->withLicense('https://bitbucket.org/multicoreware/x265_git/src/master/COPYING', Library::LICENSE_LGPL)
        //->withUrl('https://bitbucket.org/multicoreware/x265_git/downloads/x265_4.1.tar.gz')
        ->withUrl('https://bitbucket.org/multicoreware/x265_git/get/ffba52bab55dce9b1b3a97dd08d12e70297e2180.tar.gz')
        ->withFile('libx265_master.tar.gz')
        ->withManual('https://bitbucket.org/multicoreware/x265_git.git')
        ->withPrefix($libx265_prefix)
        ->withConfigure(
            <<<EOF

            cat source/CMakeLists.txt
            mkdir -p build-dir
            cd build-dir

            cmake \
            -G "Unix Makefiles" ../source \
            -DCMAKE_INSTALL_PREFIX={$libx265_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_CURRENT_SOURCE_DIR={$p->getBuildDir()}/libx265/source/ \
            -DENABLE_SHARED=OFF \
            -DENABLE_LIBNUMA=OFF \
            -DENABLE_PIC=ON \
            -DENABLE_CLI=ON \
            -DCMAKE_POLICY_VERSION_MINIMUM=3.5

            #  {$options} \
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
        ->withBinPath($libx265_prefix . '/bin/')// ->withDependentLibraries('numa')
    ;
    $p->addLibrary($lib);
};
