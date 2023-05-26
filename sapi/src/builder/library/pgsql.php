<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $pgsql_prefix = PGSQL_PREFIX;
    $p->addLibrary(
        (new Library('pgsql'))
            ->withHomePage('https://www.postgresql.org/')
            ->withLicense('https://www.postgresql.org/about/licence/', Library::LICENSE_SPEC)
            ->withUrl('https://ftp.postgresql.org/pub/source/v15.1/postgresql-15.1.tar.gz')
            ->withManual('https://www.postgresql.org/docs/current/install-procedure.html#CONFIGURE-OPTIONS')
            ->withPrefix($pgsql_prefix)
            ->withBuildScript(
                <<<EOF
            ./configure --help
            ./configure --help

            sed -i.backup "s/invokes exit\'; exit 1;/invokes exit\';/"  src/interfaces/libpq/Makefile

            # 替换指定行内容。102行，整行替换
            # sed -i.backup "102c all: all-lib" src/interfaces/libpq/Makefile
            # # 静态编译 src/interfaces/libpq/Makefile  有静态配置  参考： all-static-lib

            PACKAGES="openssl zlib icu-uc icu-io icu-i18n readline libxml-2.0  libxslt libzstd liblz4"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES )" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES )" \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES )" \
            ./configure  \
            --prefix={$pgsql_prefix} \
            --enable-coverage=no \
            --with-ssl=openssl  \
            --with-readline \
            --with-icu \
            --without-ldap \
            --with-libxml  \
            --with-libxslt \
            --with-ssl=openssl \
            --with-lz4 \
            --with-zstd \
            --without-perl \
            --without-python \
            --without-pam \
            --without-ldap \
            --without-bonjour \
            --without-tcl

            make -C src/bin install
            make -C src/include install
            make -C src/interfaces install

            make -C  src/common install
            make -C  src/port install
EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$pgsql_prefix}/lib/*.so.*
            rm -rf {$pgsql_prefix}/lib/*.so
            rm -rf {$pgsql_prefix}/lib/*.dylib
EOF
            )
            ->withPkgName('libpq')
            ->withBinPath($pgsql_prefix . '/bin/')
            ->depends('zlib', 'icu', 'libxml2', 'openssl', 'readline', 'libxslt', 'libzstd', 'liblz4')
    );
};
