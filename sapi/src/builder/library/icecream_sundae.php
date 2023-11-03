<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $lzo_prefix = LZO_PREFIX;
    $lib = new Library('icecream_sundae');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('https://github.com/JPEWdev/icecream-sundae/blob/master/LICENSE', Library::LICENSE_GPL)
        ->withManual('https://github.com/JPEWdev/icecream-sundae.git')
        ->withFile('icecream-sundae-latest.tar.gz')
        ->withDownloadScript(
            'icecream-sundae',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/JPEWdev/icecream-sundae.git
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add ninja python3 py3-pip meson
EOF
        )
        ->withPrefix($example_prefix)
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
            meson  -h
            meson setup -h
            # meson configure -h

            LIBRARY_PATH={$lzo_prefix}/lib \
            meson setup  build \
            -Dprefix={$example_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true

            ninja -C build
            ninja -C build install
EOF
        )

        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('lzo', 'icecream', 'ncurses')


    ;


    $p->addLibrary($lib);
};
