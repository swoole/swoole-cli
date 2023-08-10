<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $upnp_prefix = UPNP_PREFIX;
    $lib = new Library('upnp');
    $lib->withHomePage('https://wiki.gnome.org/Projects/GUPnP')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://download.gnome.org/sources/gupnp/1.6/gupnp-1.6.4.tar.xz')
        ->withManual('https://wiki.gnome.org/Projects/GUPnP')
        ->withUntarArchiveCommand('xz')
        ->withPrefix($upnp_prefix)
        ->withBuildLibraryCached(false)
        ->withCleanBuildDirectory()
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

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

            meson compile -C build

            ninja -C build
            ninja -C build install
EOF
        )
    ;

    $p->addLibrary($lib);
};
