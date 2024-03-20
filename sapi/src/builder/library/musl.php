<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $musl_libc_prefix = EXAMPLE_PREFIX;
    $musl_libc_prefix = MUSL_LIBC_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('musl_Libc');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://musl.libc.org/')
        ->withUrl('https://musl.libc.org/releases/musl-1.2.5.tar.gz')
        ->withPrefix($musl_libc_prefix)

        /* 使用 autoconfig automake  构建 start  */
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
        --prefix={$musl_libc_prefix} \
        --enable-shared=no \
        --enable-static=yes
EOF
        )
        ->withBinPath($musl_libc_prefix . '/bin/')
    ;
    $p->addLibrary($lib);

};
