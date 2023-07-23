<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libsrtp_prefix = LIBSRTP_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('libsrtp');
    $lib->withHomePage('https://github.com/cisco/libsrtp/')
        ->withLicense('https://github.com/cisco/libsrtp/blob/main/LICENSE', Library::LICENSE_SPEC)
        ->withUrl('https://github.com/cisco/libsrtp/archive/refs/tags/v2.5.0.tar.gz')
        ->withManual('https://github.com/cisco/libsrtp/')
        ->withFile('libsrtp-v2.5.0.tar.gz')
        ->withPrefix($libsrtp_prefix)
        ->withConfigure(
            <<<EOF
            # 修改 configure 文件以后执行，执行如下命令
            # autoremake -ivf

            ./configure --help
            PACKAGES="openssl libpcap"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) " \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) " \
            ./configure \
            --prefix={$libsrtp_prefix} \
            --enable-openssl \
            --enable-log-stdout \
            --with-openssl-dir={$openssl_prefix}
EOF
        )
        ->withBuildLibraryCached(false)
        ->withPkgName('libsrtp2')
        ->withDependentLibraries('libpcap', 'openssl');

    $p->addLibrary($lib);
};
