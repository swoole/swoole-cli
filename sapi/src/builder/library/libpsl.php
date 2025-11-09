<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libpsl_prefix = LIBPSL_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $libintl_prefix = GETTEXT_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $options = '';
    if ($p->isMacos()) {
        $options = '--with-libintl-prefix=' . $libintl_prefix;
    } else {
        $options = '--without-libintl-prefix';
    }
    $p->addLibrary(
        (new Library('libpsl'))
            ->withHomePage('https://rockdaboot.github.io/libpsl')
            ->withManual('https://github.com/rockdaboot/libpsl.git')
            ->withLicense('https://github.com/rockdaboot/libpsl/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withUrl('https://github.com/rockdaboot/libpsl/releases/download/0.21.5/libpsl-0.21.5.tar.gz')
            ->withFileHash('md5', '870a798ee9860b6e77896548428dba7b')
            ->withPrefix($libpsl_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            PACKAGES=" libidn2 "

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) -I{$libiconv_prefix}/include/ -I{$libunistring_prefix}/include/" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -L{$libiconv_prefix}/lib/ -L{$libunistring_prefix}/lib/" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) -liconv -lunistring" \
            ./configure \
            --prefix={$libpsl_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-builtin \
            --with-libiconv-prefix={$libiconv_prefix} \
            {$options}


EOF
            )
            ->withPkgName('libpsl')
            ->withDependentLibraries('libiconv', 'libunistring', 'gettext', 'libunistring')
    );

};
