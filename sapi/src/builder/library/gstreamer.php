<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $gstreamer_prefix = GSTREAMER_PREFIX;
    $lib = new Library('gstreamer');
    $lib->withHomePage('https://gstreamer.freedesktop.org/')
        ->withLicense('https://gitlab.freedesktop.org/gstreamer/gstreamer/-/blob/main/LICENSE', Library::LICENSE_LGPL)
        ->withUrl('https://gitlab.freedesktop.org/gstreamer/gstreamer/-/archive/1.20/gstreamer-1.20.tar.gz')
        ->withManual('https://gstreamer.freedesktop.org/documentation/')
        ->withFile('gstreamer-v1.20.tar.gz')
        ->withDownloadScript(
            'gstreamer',
            <<<EOF
                git clone -b 1.20  --depth=1 https://gitlab.freedesktop.org/gstreamer/gstreamer.git
EOF
        )
        ->withPrefix($gstreamer_prefix)
        ->withBuildScript(
            <<<EOF
meson build_dir
ninja -C build_dir
meson -Dauto_features=disabled -Dgstreamer:tools=enabled -Dbad=enabled -Dgst-plugins-bad:openh264=enabled
EOF
        )
    ;

    $p->addLibrary($lib);
};
