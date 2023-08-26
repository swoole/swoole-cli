<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libsoup_prefix = UPNP_PREFIX;
    $libsoup_prefix = LIBSOUP_PREFIX;
    $lib = new Library('libsoup');
    $lib->withHomePage('https://gitlab.gnome.org/GNOME/libsoup')
        ->withLicense('https://gitlab.gnome.org/GNOME/libsoup/-/blob/master/COPYING', Library::LICENSE_GPL)
        ->withManual('https://gitlab.gnome.org/GNOME/libsoup')
        ->withFile('libsoup-latest.tar.gz')
        ->withDownloadScript(
            'libsoup',
            <<<EOF
        git clone -b master --depth=1 https://gitlab.gnome.org/GNOME/libsoup.git
EOF
        )
        ->withPrefix($libsoup_prefix)
        ->withBuildLibraryCached(false)
        ->withCleanBuildDirectory()
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h
            test -d build && rm -rf build
            meson setup  build \
            -Dprefix={$libsoup_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dgssapi=disabled \
            -Dntlm=disabled \
            -Dntlm_auth=disabled \
            -Dbrotli=enabled \
            -Dbrotli=enabled \
            -Dtls_check=true \
            -Dtests=false \
            -Dpkcs11_tests=disabled

            ninja -C build
            ninja -C build install
EOF
        )
        ->withDependentLibraries('brotli', 'glib', 'nghttp2', 'sqlite3', 'libpsl')
    ;

    $p->addLibrary($lib);
};
