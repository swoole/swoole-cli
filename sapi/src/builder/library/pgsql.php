<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $pgsql_prefix = PGSQL_PREFIX;
    $option = '';
    $ldflags = $p->getOsType() == 'macos' ? '' : ' -static ';
    if ($p->getOsType() == 'macos') {
        $option = '--disable-thread-safety';
    }
    $p->addLibrary(
        (new Library('pgsql'))
            ->withHomePage('https://www.postgresql.org/')
            ->withLicense('https://www.postgresql.org/about/licence/', Library::LICENSE_SPEC)
            ->withUrl('https://ftp.postgresql.org/pub/source/v15.1/postgresql-15.1.tar.gz')
            ->withManual('https://www.postgresql.org/docs/current/install-procedure.html#CONFIGURE-OPTIONS')
            ->withManual('https://www.postgresql.org/docs/current/install-procedure.html#CONFIGURE-OPTIONS#:~:text=Client-only%20installation')
            ->withPrefix($pgsql_prefix)
            ->withBuildScript(
                <<<EOF
            test -d build && rm -rf build
            mkdir -p build
            cd build

            ../configure --help

            # 有静态链接配置  参考文件： src/interfaces/libpq/Makefile

            # 静态链接方法一：
            # 121行 替换内容

            sed -i.backup "s/invokes exit\'; exit 1;/invokes exit\';/"  ../src/interfaces/libpq/Makefile
            sed -i.backup "293 s/^/#$/"  ../src/Makefile.shlib
            sed -i.backup "441 s/^/#$/"  ../src/Makefile.shlib

            # 静态链接方法二：
            # 102行，整行替换
            # sed -i.backup "102c all: all-lib" ../src/interfaces/libpq/Makefile

            PACKAGES="openssl zlib icu-uc icu-io icu-i18n readline libxml-2.0  libxslt libzstd liblz4"
            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES )" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) {$ldflags} " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES )" \
            ../configure  \
            --prefix={$pgsql_prefix} \
            --enable-coverage=no \
            {$option} \
            --with-ssl=openssl  \
            --with-readline \
            --with-icu \
            --without-ldap \
            --with-libxml  \
            --with-libxslt \
            --with-lz4 \
            --with-zstd \
            --without-perl \
            --without-python \
            --without-pam \
            --without-ldap \
            --without-bonjour \
            --without-tcl



            make -C src/bin/pg_config install
            make -C src/include install

            make -C  src/common install

            make -C  src/backend/port install
            make -C  src/port install

            make -C  src/backend/libpq install
            make -C  src/interfaces/libpq install

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
            ->withDependentLibraries('zlib', 'icu', 'libxml2', 'openssl', 'readline', 'libxslt', 'libzstd', 'liblz4')
    );
};
