<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $gvdb_prefix = GVDB_PREFIX;
    $lib = new Library('gvdb');
    $lib->withHomePage('https://gitlab.gnome.org/GNOME/gvdb/')
        ->withLicense('https://gitlab.gnome.org/GNOME/gvdb/-/blob/main/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://gitlab.gnome.org/GNOME/gvdb/')
        ->withFile('gvdb-latest.tar.gz')
        ->withDownloadScript(
            'gvdb',
            <<<EOF
                git clone -b main --depth=1 https://gitlab.gnome.org/GNOME/gvdb.git
EOF
        )
        ->withPrefix($gvdb_prefix)
        ->withBuildScript(
            <<<EOF
            test -d build && rm -rf build
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$gvdb_prefix} \
            -Dlibdir={$gvdb_prefix}/lib \
            -Dincludedir={$gvdb_prefix}/include \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true


            meson compile -C build
            meson install -C build
EOF
        )

        ->withPkgName('glib-2.0')
        ->withBinPath($gvdb_prefix . '/bin/')
    ;
    $p->addLibrary($lib);

};
