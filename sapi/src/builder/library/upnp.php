<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $upnp_prefix = UPNP_PREFIX;
    $lib = new Library('upnp');
    $lib->withHomePage('https://wiki.gnome.org/Projects/GUPnP')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://wiki.gnome.org/Projects/GUPnP')
        ->withFile('gupnp-latest.tar.gz')
        ->withDownloadScript(
            'gupnp',
            <<<EOF
        git clone -b master --depth=1 https://gitlab.gnome.org/GNOME/gupnp.git
EOF
        )
        ->withPrefix($upnp_prefix)
        ->withBuildCached(false)
        ->withCleanBuildDirectory()
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h
            test -d build && rm -rf build
            meson setup  build \
            -Dprefix={$upnp_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dexamples=false \
            -Dgtk_doc=false \
            -Dintrospection=true \
            -Dvapi=true

            # meson compile -C build

            ninja -C build
            ninja -C build install
EOF
        )
        ->withDependentLibraries('glib', 'libsoup')
    ;

    $p->addLibrary($lib);
};
