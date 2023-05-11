<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $pgsql_prefix = PGSQL_PREFIX;
    $p->addLibrary(
        (new Library('pgsql'))
            ->withHomePage('https://www.postgresql.org/')
            ->withLicense('https://www.postgresql.org/about/licence/', Library::LICENSE_SPEC)
            ->withUrl('https://ftp.postgresql.org/pub/source/v15.1/postgresql-15.1.tar.gz')
            ->withPrefix($pgsql_prefix)
            ->withconfigure(
                <<<EOF
            ./configure --help

            sed -i.backup "s/invokes exit\'; exit 1;/invokes exit\';/"  src/interfaces/libpq/Makefile
            # 替换指定行内容。102行，整行替换
            sed -i.backup "102c all: all-lib" src/interfaces/libpq/Makefile
            package_names="openssl zlib icu-uc icu-io icu-i18n readline libxml-2.0  libxslt libzstd liblz4"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$package_names )" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$package_names )" \
            LIBS="$(pkg-config      --libs-only-l   --static \$package_names ) -lstdc++" \
            ./configure  \
            --prefix={$pgsql_prefix} \
            --enable-coverage=no \
            --with-ssl=openssl  \
            --with-readline \
            --with-icu \
            --without-ldap \
            --with-libxml  \
            --with-libxslt

:<<'==EOF=='
            result_code=$?
            [[ \$result_code -ne 0 ]] && echo "[make FAILURE]" && exit \$result_code;
            make -C src/include install

            make -C  src/bin/pg_config install

            make -C  src/common -j \$cpu_nums all
            make -C  src/common install

            make -C  src/port -j \$cpu_nums all
            make -C  src/port install

            make -C  src/backend/libpq -j \$cpu_nums all
            make -C  src/backend/libpq install

            make -C src/interfaces/ecpg   -j \$cpu_nums all-pgtypeslib-recurse all-ecpglib-recurse all-compatlib-recurse all-preproc-recurse
            make -C src/interfaces/ecpg  install-pgtypeslib-recurse install-ecpglib-recurse install-compatlib-recurse install-preproc-recurse

            # 静态编译 src/interfaces/libpq/Makefile  有静态配置  参考： all-static-lib

            make -C src/interfaces/libpq  -j \$cpu_nums # soname=true
            make -C src/interfaces/libpq  install
==EOF==

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
    $pdo_pgsql_version=$p::SWOOLE_CLI_PHP_VERSION;
    $p->addExtension(
        (new Extension('pdo_pgsql'))
            ->withOptions('--with-pdo-pgsql=' . PGSQL_PREFIX)
            ->withPeclVersion($pdo_pgsql_version)
            ->withDownloadScript(
                "pdo_pgsql",
                <<<EOF
                test -d php-src && rm -rf php-src
                git clone -b php-{$pdo_pgsql_version} --depth=1 https://github.com/php/php-src.git
                cp -rf php-src/ext/pdo_pgsql pdo_pgsql
EOF
            )
            ->depends('pgsql')
    );
};
