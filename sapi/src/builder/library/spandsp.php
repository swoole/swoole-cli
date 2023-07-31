<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $spandsp_prefix = SPANDSP_PREFIX;
    $lib = new Library('spandsp');
    $lib->withHomePage('https://github.com/freeswitch/spandsp.git')
        ->withLicense('https://github.com/freeswitch/spandsp/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://github.com/freeswitch/spandsp.git')
        ->withFile('spandsp-latest-jingjingxyk.tar.gz')
        ->withDownloadScript(
            'spandsp',
            <<<EOF
                # git clone -b master  --depth=1 https://github.com/freeswitch/spandsp.git
                git clone -b patch  --depth=1 https://github.com/jingjingxyk/spandsp.git
EOF
        )
        ->withPrefix($spandsp_prefix)
        ->withBuildLibraryHttpProxy()
        ->withPreInstallCommand('alpine', <<<EOF
        apk add util-linux
EOF
        )
        ->withConfigure(
            <<<EOF

            # 需要修复这个问题 error: attribute '__packed__' is ignored, place it after "struct" to apply attribute to type declaration
            # https://github.com/freeswitch/spandsp/pull/58

            # 临时解决
            # https://github.com/piligrim773/spandsp/blob/fix/spandsp/unaligned.h/src/spandsp/unaligned.h?raw=true

            curl -Lo src/spandsp/unaligned.h https://raw.githubusercontent.com/piligrim773/spandsp/fix/spandsp/unaligned.h/src/spandsp/unaligned.h

            # curl -Lo src/spandsp/v150_1.h https://github.com/jingjingxyk/spandsp/blob/patch/src/spandsp/v150_1.h?raw=true

            sh ./autogen.sh
            ./configure --help
            PACKAGES="libtiff-4"

            # -Wc11-extensions
            CFLAGS="-O3  -g  -fms-extensions -Wc++11-extensions  -std=c11" \
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
