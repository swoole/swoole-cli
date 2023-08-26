<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $glib_prefix = EXAMPLE_PREFIX;
    $glib_prefix = GLIB_PREFIX;
    $lib = new Library('glib');
    $lib->withHomePage('https://gitlab.gnome.org/GNOME/glib')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://gitlab.gnome.org/GNOME/glib/-/blob/main/INSTALL.md')
        ->withFile('glib-latest.tar.gz')
        ->withDownloadScript(
            'glib',
            <<<EOF
                git clone -b main --depth=1 https://gitlab.gnome.org/GNOME/glib.git
EOF
        )
        ->withPrefix($glib_prefix)
        /*
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($glib_prefix)
        ->withBuildLibraryCached(false)
        */
        ->withBuildLibraryHttpProxy()
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$glib_prefix} \
            -Dlibdir={$glib_prefix}/lib \
            -Dincludedir={$glib_prefix}/include \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dglib_debug=disabled \
            -Dglib_assert=false \
            -Dtests=false

            meson compile -C build
            meson install -C build
EOF
        )

        ->withPkgName('glib-2.0')
        ->withBinPath($glib_prefix . '/bin/')
        ->withDependentLibraries('pcre2', 'libffi', 'zlib'); //'gvdb' , 'proxy-libintl'
    $p->addLibrary($lib);
};
