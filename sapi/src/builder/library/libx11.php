<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libx11_prefix = LIBX11_PREFIX;
    $xorg_util_macros_prefix = XORG_UTIL_MACROS_PREFIX;
    $lib = new Library('libx11');
    $lib->withHomePage('https://gitlab.freedesktop.org/xorg/lib/libx11')
        ->withLicense('https://gitlab.freedesktop.org/xorg/lib/libx11/-/blob/master/COPYING', Library::LICENSE_BSD)
        ->withManual('https://github.com/opencv/opencv.git')
        //->withUrl('https://xorg.freedesktop.org/archive/individual/lib/xcb-util-0.4.1.tar.gz')

        ->withFile('libx11-v1.8.6.tar.gz')
        ->withDownloadScript(
            'libx11',
            <<<EOF
                git clone -b libX11-1.8.6  --depth=1 https://gitlab.freedesktop.org/xorg/lib/libx11.git
EOF
        )


        ->withPrefix($libx11_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libx11_prefix)
        ->withBuildLibraryCached(false)

        ->withConfigure(
            <<<EOF

            ACLOCAL_PATH={$xorg_util_macros_prefix}/share/aclocal sh autogen.sh
            ./configure --help

            PACKAGES='xorg-macros  '
            PACKAGES="\$PACKAGES "

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$libx11_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --without-perl \
             --without-launchd
EOF
        )

           //ACLOCAL_PATH=/path/to/my/share/aclocal
           //# depends_dev libxcb-dev util-macros

        ->withPkgName('opencv')
        ->withBinPath($libx11_prefix . '/bin/')
        ->withDependentLibraries('xorg_util_macros') //依赖其它静态依赖库
        ;
    $p->addLibrary($lib);

   //参考 https://github.com/BtbN/FFmpeg-Builds/tree/master/scripts.d/45-x11
};
