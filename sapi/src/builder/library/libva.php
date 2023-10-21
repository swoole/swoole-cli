<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libva_prefix = EXAMPLE_PREFIX;
    $libva_prefix = LIBVA_PREFIX;


    // VA API(Video Acceleration API)
    //https://github.com/intel/libva.git
    //https://github.com/intel/libva-utils

    //FFmpeg+VAAPI的硬解方案

    $lib = new Library('libva');
    $lib->withHomePage('https://01.org/linuxmedia')
        ->withLicense('https://github.com/intel/libva/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withManual('https://github.com/intel/libva.git')
        ->withFile('libva-2.0.0.tar.gz')
        ->withDownloadScript(
            'libva',
            <<<EOF
                git clone -b libva-2.0.0  --depth=1 https://github.com/intel/libva.git
EOF
        )
        ->withPrefix($libva_prefix)
        ->withConfigure(
            <<<EOF
        meson  -h
        meson setup -h

        meson setup  build \
        -Dprefix={$libva_prefix} \
        -Dlibdir={$libva_prefix}/lib \
        -Dincludedir={$libva_prefix}/include \
        -Dbackend=ninja \
        -Dbuildtype=release \
        -Ddefault_library=static \
        -Db_staticpic=true \
        -Db_pie=true \
        -Dprefer_static=true \
        -Ddisable_drm=true \
        -Dwith_x11=no \
        -Dwith_glx=no  \
        -Dwith_wayland=yes \
        -Denable_docs=false

        ninja -C build
        ninja -C build install

EOF
        )
        ->withConfigure(
            <<<EOF
        sh autogen.sh


        ./configure --help


        PACKAGES='wayland-client libdrm '
        PACKAGES="\$PACKAGES "

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
        ./configure \
        --prefix={$libva_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --enable-docs=no \
        --enable-drm=yes \
        --enable-x11=no \
        --enable-glx=no \
        --enable-wayland=yes \
        --enable-va-messaging=yes


EOF
        )
        ->withPkgName('libva-drm')
        ->withPkgName('libva-wayland')
        ->withPkgName('libva')
        ->withBinPath($libva_prefix . '/bin/')
        ->withDependentLibraries('wayland', 'libdrm')


    ;

    $p->addLibrary($lib);


};


# 图形界面GUI相关概念GLX/Wayland/X11/DRM/DRI
# https://blog.csdn.net/qq_23662505/article/details/130341569
