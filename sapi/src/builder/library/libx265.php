<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libx265_prefix = LIBX265_PREFIX;

    $lib = new Library('libx265');
    $lib->withHomePage('https://www.videolan.org/developers/x265.html')
        ->withLicense('https://bitbucket.org/multicoreware/x265_git/src/master/COPYING', Library::LICENSE_LGPL)
        //->withUrl('http://ftp.videolan.org/pub/videolan/x265/x265_2.7.tar.gz')
        //->withFile('libx265_2.7.tar.gz')
        ->withFile('libx265_Release_3.5.tar.gz')
        ->withDownloadScript(
            'x265_git',
            <<<EOF
            git clone -b Release_3.5 --progress --depth=1 https://bitbucket.org/multicoreware/x265_git.git
EOF
        )
        ->withManual('https://bitbucket.org/multicoreware/x265_git.git')
        ->withPrefix($libx265_prefix)
        ->withCleanPreInstallDirectory($libx265_prefix)
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

            # bug  -lgcc -lgcc_s -lc -lgcc -lgcc_s
            # set(CMAKE_C_IMPLICIT_LINK_LIBRARIES "ssp_nonshared;gcc;gcc_s;c;gcc;gcc_s")  CMakeFiles/3.24.4/CMakeCCompiler.cmake
            # sed -i.save s@\${CMAKE_C_IMPLICIT_LINK_LIBRARIES}@@ CMakeLists.txt

            cmake \
            -G"Unix Makefiles" ../source  \
            -DCMAKE_INSTALL_PREFIX={$libx265_prefix} \
            -DCMAKE_C_COMPILER={$p->get_C_COMPILER()} \
            -DCMAKE_CXX_COMPILER={$p->get_CXX_COMPILER()} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_POLICY_DEFAULT_CMP0075=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DENABLE_SHARED=OFF


EOF
        )
        //默认不需要此配置
        ->withScriptAfterInstall(
            <<<EOF
                mkdir -p {$libx265_prefix}/lib/pkgconfig/
                cat > {$libx265_prefix}/lib/pkgconfig/x265.pc <<'__libx265__EOF'
prefix={$libx265_prefix}
exec_prefix=\${prefix}
libdir=\${exec_prefix}/lib
includedir=\${prefix}/include

Name: x265
Description: H.265 encoder library
Version: 3.5.x
Libs: -L\${exec_prefix}/lib -lx265
Libs.private:
Cflags: -I\${prefix}/include

__libx265__EOF
EOF
        )

        ->withPkgName('x265')
        ->withBinPath($libx265_prefix . '/bin/')
        // ->withDependentLibraries('numa')
    ;
    $p->addLibrary($lib);
};
