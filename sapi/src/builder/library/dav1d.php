<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $dav1d_prefix = DAV1D_PREFIX;
    $linux_pre_install=<<<EOF
        # library dav1d :
        apk add ninja python3 py3-pip  nasm yasm
        pip3 install meson
EOF;
    $macos_pre_install=<<<EOF

export HOMEBREW_INSTALL_FROM_API=1
export HOMEBREW_NO_ANALYTICS=1
export HOMEBREW_NO_AUTO_UPDATE=1

brew install  ninja python3  nasm
# python3 -m pip install --upgrade pip
pip3 install meson
# curl https://bootstrap.pypa.io/get-pip.py -o get-pip.py
EOF;
    $pre_install = $p->getOsType()=='macos' ? $macos_pre_install : $linux_pre_install;
    $p->addLibrary(
        (new Library('dav1d'))
            ->withHomePage('https://code.videolan.org/videolan/dav1d/')
            ->withLicense('https://code.videolan.org/videolan/dav1d/-/blob/master/COPYING', Library::LICENSE_BSD)
            ->withManual('https://code.videolan.org/videolan/dav1d')
            //->withUrl('https://code.videolan.org/videolan/dav1d/-/archive/1.2.1/dav1d-1.2.1.tar.gz')
            //->withFile('dav1d-1.2.1.tar.gz')
            ->withFile('dav1d-git-1.2.1.tar.gz')
            ->withDownloadScript(
                'dav1d',
                <<<EOF
                git clone -b 1.2.1 --depth=1 --progress https://code.videolan.org/videolan/dav1d.git
EOF
            )
            ->withPrefix($dav1d_prefix)
            ->withBuildLibraryCached(true)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($dav1d_prefix)
            ->withPreInstallCommand($pre_install)
            ->withBuildScript(
                <<<EOF

                meson setup  build \
                -Dprefix={$dav1d_prefix} \
                -Dbackend=ninja \
                -Dbuildtype=release \
                -Ddefault_library=static \
                -Db_staticpic=true \
                -Db_pie=true \
                -Dprefer_static=true \
                -Denable_asm=true \
                -Denable_tools=true \
                -Denable_examples=false \
                -Denable_tests=false \
                -Denable_docs=false \
                -Dlogging=false \
                -Dfuzzing_engine=none


                meson compile -C build

                ninja -C build
                ninja -C build install


EOF
            )
            ->withPkgName('dav1d')
            ->withBinPath($dav1d_prefix . '/bin/')
            ->withDependentLibraries('sdl2')
    );
};
