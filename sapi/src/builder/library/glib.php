<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('glib');
    $lib->withHomePage('https://gitlab.gnome.org/GNOME/glib')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://gitlab.gnome.org/GNOME/glib/-/blob/main/INSTALL.md')
        ->withFile('glib-latest.tar.gz')
        ->withAutoUpdateFile()
        ->withDownloadScript(
            'glib',
            <<<EOF
                git clone -b main --depth=1 https://gitlab.gnome.org/GNOME/glib.git
EOF
        )
        ->withUntarArchiveCommand('xz')
        ->withPrefix($example_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($example_prefix)
        ->withBuildLibraryCached(false)
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            meson setup  build \
            -Dprefix={$example_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dexamples=disabled

            meson compile -C build
            meson install -C build
EOF
        )

        ->withPkgName('glib-2.0')
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'openssl');
    $p->addLibrary($lib);

};
