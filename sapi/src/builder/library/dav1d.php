<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $dav1d_prefix = DAV1D_PREFIX;
    $p->addLibrary(
        (new Library('dav1d'))
            ->withHomePage('https://code.videolan.org/videolan/dav1d/')
            ->withLicense('https://code.videolan.org/videolan/dav1d/-/blob/master/COPYING', Library::LICENSE_BSD)
            ->withManual('https://code.videolan.org/videolan/dav1d')
            ->withFile('dav1d-v1.3.0.tar.gz')
            ->withDownloadScript(
                'dav1d',
                <<<EOF
                git clone -b 1.3.0 --depth=1 --progress https://code.videolan.org/videolan/dav1d.git
EOF
            )
            ->withPrefix($dav1d_prefix)
            ->withBuildCached(false)
            ->withBuildScript(
                <<<EOF
            mkdir build
            meson setup  build  \
            -Dprefix={$dav1d_prefix} \
            -Dlibdir={$dav1d_prefix}/lib \
            -Dincludedir={$dav1d_prefix}/include \
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

            ninja -C build
            ninja -C build install

EOF
            )
            ->withPkgName('dav1d')
            ->withBinPath($dav1d_prefix . '/bin/')
    );
};
