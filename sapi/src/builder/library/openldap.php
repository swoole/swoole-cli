<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openldap_prefix = OPENLDAP_PREFIX;
    $iconv_prefix = ICONV_PREFIX;

    $lib = new Library('openldap');
    $lib->withHomePage('https://www.openldap.org/')
        ->withLicense('https://www.openldap.org/software/release/license.html', Library::LICENSE_SPEC)
        ->withManual('https://git.openldap.org/openldap/openldap.git')
        ->withFile('openldap-v2.6.6.tar.gz')
        ->withDownloadScript(
            'openldap',
            <<<EOF
        git clone -b OPENLDAP_REL_ENG_2_6_6  --depth=1 https://git.openldap.org/openldap/openldap.git
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add groff
EOF
        )
        ->withPreInstallCommand(
            'debian',
            <<<EOF
        apt-get -y install groff-base
EOF
        )
        ->withPrefix($openldap_prefix)
        ->withConfigure(
            <<<EOF
        ./configure --help
        set -x
        PACKAGES='openssl  libssl libcrypto'
        PACKAGES="\$PACKAGES zlib"
        PACKAGES="\$PACKAGES libargon2"
        PACKAGES="\$PACKAGES odbc odbccr odbcinst"
        PACKAGES="\$PACKAGES gmp"
        PACKAGES="\$PACKAGES readline"

        CPPFLAGS="\$(pkg-config --cflags-only-I --static \$PACKAGES ) -I{$iconv_prefix}/include" \
        LDFLAGS="\$(pkg-config  --libs-only-L   --static \$PACKAGES ) -L{$iconv_prefix}/lib"  \
        LIBS="\$(pkg-config     --libs-only-l   --static \$PACKAGES ) -liconv" \
        ./configure \
        --prefix={$openldap_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --without-systemd \
        --with-tls=openssl \
        --with-mp=gmp \
        --with-odbc=unixodbc \
        --with-argon2=libargon2

EOF
        )

        //依赖其它静态链接库
        ->withDependentLibraries(
            'zlib',
            'openssl',
            'gmp',
            'libargon2',
            'unix_odbc',
            'readline',
            'libiconv',
            'libedit'
        )
        ->withPkgName('lber')
        ->withPkgName('ldap')
        ->withBinPath($openldap_prefix . '/bin/:' . $openldap_prefix . '/sbin/:' . $openldap_prefix . 'libexec');

    $p->addLibrary($lib);
};
