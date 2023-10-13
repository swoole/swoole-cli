<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $GUPnP_prefix = GUPnP_PREFIX;
    $lib = new Library('gupnp_igd');
    $lib->withHomePage('https://wiki.gnome.org/Projects/GUPnP')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://download.gnome.org/sources/gupnp-igd/1.6/gupnp-igd-1.6.0.tar.xz')
        ->withManual('https://wiki.gnome.org/Projects/GUPnP')
        ->withUntarArchiveCommand('xz')
        ->withPrefix($GUPnP_prefix)
        ->withBuildCached(false)
        ->withCleanBuildDirectory()
        ->withBuildScript(
            <<<EOF
             meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$GUPnP_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dgtk_doc=false


            meson compile -C build

            ninja -C build
            ninja -C build install

EOF
        )
    ;

    $p->addLibrary($lib);
};
