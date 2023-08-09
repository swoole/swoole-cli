<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('libudfread');
    $lib->withHomePage('https://code.videolan.org/videolan/libudfread')
        ->withLicense('https://code.videolan.org/videolan/libudfread/-/blob/master/COPYING', Library::LICENSE_LGPL)
        ->withManual('https://code.videolan.org/videolan/libudfread.git')
        ->withFile('libudfread-1.1.2.tar.gz')
        ->withDownloadScript(
            'libudfread',
            <<<EOF
                git clone -b 1.1.2  --depth=1 https://code.videolan.org/videolan/libudfread.git
EOF
        )
        ->withConfigure(
            <<<EOF
            autoreconf -vif
            ./configure --help

            PACKAGES='openssl  '
            PACKAGES="\$PACKAGES zlib"

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -static" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$example_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --disable-shared \
            --enable-static \
            --with-pic

EOF
        )
        ->withPkgName('libudfread')
        ->withBinPath($example_prefix . '/bin/')
    ;


    $p->addLibrary($lib);
};
