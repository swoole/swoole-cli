<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $spandsp_prefix = SPANDSP_PREFIX;
    $lib = new Library('spandsp');
    $lib->withHomePage('https://github.com/freeswitch/spandsp.git')
        ->withLicense('https://github.com/freeswitch/spandsp/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/freeswitch/spandsp.git')
        ->withFile('spandsp-latest.tar.gz')
        ->withDownloadScript(
            'spandsp',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/freeswitch/spandsp.git
EOF
        )
        ->withPrefix($spandsp_prefix)
        ->withConfigure(
            <<<EOF

            sh ./autogen.sh
            ./configure --help
            PACKAGES="libtiff-4"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) " \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) " \
            ./configure \
             --prefix={$spandsp_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withPkgName('spandsp')
        ->withDependentLibraries('libtiff'); //'libaudiofile'
    ;

    $p->addLibrary($lib);
};
