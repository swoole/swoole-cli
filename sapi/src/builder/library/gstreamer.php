<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $gstreamer_prefix = GSTREAMER_PREFIX;
    $lib = new Library('gstreamer');
    $lib->withHomePage('https://gstreamer.freedesktop.org/')
        ->withLicense('https://gitlab.freedesktop.org/gstreamer/gstreamer/-/blob/main/LICENSE', Library::LICENSE_LGPL)
        ->withUrl('https://gitlab.freedesktop.org/gstreamer/gstreamer/-/archive/1.20/gstreamer-1.20.tar.gz')
        ->withManual('https://gstreamer.freedesktop.org/documentation/installing/index.html?gi-language=c')
        ->withManual('https://gstreamer.freedesktop.org/documentation/')
        ->withFile('gstreamer-v1.20.tar.gz')
        ->withDownloadScript(
            'gstreamer',
            <<<EOF
            git clone -b 1.20  --depth=1 https://gitlab.freedesktop.org/gstreamer/gstreamer.git
EOF
        )
        ->withPrefix($gstreamer_prefix)
        ->withBuildLibraryHttpProxy()
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$gstreamer_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dexamples=disabled \
            -Dauto_features=disabled \
            -Dgstreamer:tools=enabled \
            -Dbad=enabled \
            -Dgst-plugins-bad:openh264=enabled \
            -Dlibnice:tests=disabled \
            -Dlibnice:examples=disabled \
            -Dopenh264:tests=disabled \
            -Dpygobject:tests=false \
            -Dpython=disabled \
            -Dlibav=enabled \
            -Dugly=enabled \
            -Dbad=enabled \
            -Ddevtools=disabled \
            -Dges=enabled \
            -Drtsp_server=enabled \
            -Dvaapi=enabled \
            -Dsharp=disabled \
            -Dgpl=enabled

            meson compile -C build

            ninja -C build
            ninja -C build install
EOF
        )
        ->withDependentLibraries('gmp')
    ;

    $p->addLibrary($lib);
};
