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
            ->withPrefix($pgsql_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help

            sed -i.backup "s/invokes exit\'; exit 1;/invokes exit\';/"  src/interfaces/libpq/Makefile

            # 替换指定行内容。102行，整行替换
            # sed -i.backup "102c all: all-lib" src/interfaces/libpq/Makefile

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

EOF
            )
            ->withPkgName('libpq')
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$pgsql_prefix}/lib/*.so.*
            rm -rf {$pgsql_prefix}/lib/*.so
            rm -rf {$pgsql_prefix}/lib/*.dylib
EOF
            )
            ->depends('zlib', 'icu', 'libxml2', 'openssl', 'readline', 'libxslt', 'libzstd', 'liblz4')
    );
};
