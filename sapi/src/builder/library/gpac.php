<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $gpac_prefix = EXAMPLE_PREFIX;
    $gpac_prefix = GPAC_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;
    $cares_prefix = CARES_PREFIX;

    $lib = new Library('gpac');
    $lib->withHomePage('https://gpac.io/')
        ->withLicense('https://github.com/gpac/gpac#LGPL-2.1-1-ov-file', Library::LICENSE_LGPL)
        ->withManual('https://wiki.gpac.io/Build/Build-Introduction/')
        /* 下载依赖库源代码方式二 start */
        ->withFile('gpac-latest.tar.gz')
        ->withDownloadScript(
            'gpac',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/gpac/gpac.git
                cd gpac
                git submodule update --init --recursive --force --checkout
                cd ..
EOF
        )
        ->withPrefix($gpac_prefix)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
            apk add yasm
EOF
        )
        ->withConfigure(
            <<<EOF

        ./configure --help

        # LDFLAGS="\$LDFLAGS -static"

        PACKAGES='openssl  '
        PACKAGES="\$PACKAGES zlib"

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
        ./configure \
        --prefix={$gpac_prefix} \
        --static-build \
        --static-bin \
        --disable-x11 \
        --disable-x11-shm \
        --disable-x11-xv

EOF
        )

        ->withPkgName('example')
        ->withBinPath($gpac_prefix . '/bin/')

        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl')
        ->withPkgName('libexample')
        ->withBinPath($gpac_prefix . '/bin/')
        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'libpng')
    ;

    $p->addLibrary($lib);

};

