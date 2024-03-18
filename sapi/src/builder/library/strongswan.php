<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('strongswan');
    $lib->withHomePage('https://www.strongswan.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://www.strongswan.org/')
        ->withUrl('https://github.com/strongswan/strongswan/releases/download/5.9.13/strongswan-5.9.13.tar.gz')
        ->withFile('strongswan-5.9.13.tar.gz')
        ->withPrefix($example_prefix)
        ->withConfigure(
            <<<EOF

        ./configure --help

        # LDFLAGS="\$LDFLAGS -static"

        PACKAGES='openssl  '
        PACKAGES="\$PACKAGES zlib"
        PACKAGES="\$PACKAGES gmp"

        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
        ./configure \
        --prefix={$example_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --enable-openssl

EOF
        )


        ->withPkgName('example')
        ->withBinPath($example_prefix . '/bin/')
        ->withDependentLibraries('zlib', 'openssl','gmp')

    ;

    $p->addLibrary($lib);

};
