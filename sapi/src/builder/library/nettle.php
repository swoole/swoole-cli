<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $nettle_prefix = NETTLE_PREFIX;
    $p->addLibrary(
        (new Library('nettle'))
            ->withHomePage('https://www.lysator.liu.se/~nisse/nettle/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://ftp.gnu.org/gnu/nettle/nettle-3.8.tar.gz')
            ->withFile('nettle-3.8.tar.gz')
            ->withPrefix($nettle_prefix)
            ->withConfigure(
                <<<EOF
             ./configure --help
            PACKAGES="\$PACKAGES openssl gmp"
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
            ./configure \
            --prefix={$nettle_prefix} \
            --libdir={$nettle_prefix}/lib \
            --enable-static \
            --disable-shared \
            --enable-mini-gmp  \
            --enable-openssl
EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            sed -i.backup "s/-ldl/  /g" {$nettle_prefix}/lib/pkgconfig/hogweed.pc
EOF
            )
            ->withPkgName('nettle')
            ->withPkgName('hogweed')
            ->withDependentLibraries('gmp', 'openssl')
            ->withBinPath($nettle_prefix . '/bin/')
    );
};
