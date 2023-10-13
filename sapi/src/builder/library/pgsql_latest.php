<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $pgsql_prefix = PGSQL_LATEST_PREFIX;
    $gettext_prefix = GETTEXT_PREFIX;
    $util_linux_prefix = UTIL_LINUX_PREFIX;
    $ncurses_prefix = NCURSES_PREFIX;

    $ldflags = $p->getOsType() == 'macos' ? '' : ' -static  ';
    $libs = $p->getOsType() == 'macos' ? '-lc++' : ' -lstdc++ ';

    $p->addLibrary(
        (new Library('pgsql_latest'))
            ->withHomePage('https://www.postgresql.org/')
            ->withLicense('https://www.postgresql.org/about/licence/', Library::LICENSE_SPEC)
            ->withManual('https://www.postgresql.org/docs/current/install-procedure.html#CONFIGURE-OPTIONS')
            ->withManual('https://www.postgresql.org/download/')
            ->withManual('https://git.postgresql.org/gitweb/?p=postgresql.git;a=summary')
            ->withManual('https://www.postgresql.org/docs/current/install-procedure.html#CONFIGURE-OPTIONS#:~:text=Client-only%20installation')
            ->withFile('postgresql-latest.tar.gz')
            ->withHttpProxy(true, true)
            ->withDownloadScript(
                'postgresql',
                <<<EOF
                git clone -b master --depth=1 git://git.postgresql.org/git/postgresql.git
                # git clone -b REL_16_STABLE --depth=1 git://git.postgresql.org/git/postgresql.git
                # git clone -b REL_15_4 --depth=1 git://git.postgresql.org/git/postgresql.git
EOF
            )
            //->withAutoUpdateFile()
            ->withPrefix($pgsql_prefix)
            /*
                https://git.postgresql.org/gitweb/

                git://git.postgresql.org/git/postgresql.git
                https://git.postgresql.org/git/postgresql.git
                ssh://git@git.postgresql.org/postgresql.git
            */

            /*
                ->withCleanBuildDirectory()
                ->withCleanPreInstallDirectory($pgsql_prefix)
                ->withBuildCached(false)
            */
            ->withBuildCached(false)
            ->withBuildScript(
                <<<EOF
            # reference
            # https://git.postgresql.org/gitweb/?p=postgresql.git;a=blob;f=meson.build;
                        meson  -h
            meson setup -h
            # meson configure -h

            CPPFLAGS="-I{$gettext_prefix}/include -I{$util_linux_prefix}/include -I{$ncurses_prefix}/include" \
            LDFLAGS="-L{$gettext_prefix}/lib -L{$util_linux_prefix}/lib -L{$ncurses_prefix}/lib" \
            LIBS=" -lintl -luuid -lncurses -lncursesw " \
            meson setup  build \
            -Dprefix={$pgsql_prefix} \
            -Dlibdir={$pgsql_prefix}/lib \
            -Dincludedir={$pgsql_prefix}/include \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Dbonjour=disabled \
            -Dbsd_auth=disabled \
            -Ddocs_pdf=disabled \
            -Dgssapi=disabled \
            -Dbonjour=disabled \
            -Dicu=enabled \
            -Dldap=enabled \
            -Dlibedit_preferred=true \
            -Dlibxml=enabled \
            -Dlibxslt=enabled \
            -Dlz4=enabled \
            -Dnls=enabled \
            -Dpam=disabled \
            -Dplperl=disabled \
            -Dplpython=disabled \
            -Dpltcl=disabled \
            -Dreadline=enabled \
            -Dssl=openssl \
            -Dbonjour=disabled \
            -Dsystemd=disabled \
            -Dzlib=enabled \
            -Dzstd=enabled \


            # -Duuid=e2fs \
            # -Duuid=ossp

            # ninja -C build
            # ninja -C build install
EOF
            )
            ->withBuildScript(
                <<<EOF

            sed -i.backup "s/invokes exit\'; exit 1;/invokes exit\';/" ../src/interfaces/libpq/Makefile

            sed -i.backup "278 s/^/# /"  ../src/Makefile.shlib
            sed -i.backup "402 s/^/# /"  ../src/Makefile.shlib

            PACKAGES="openssl zlib icu-uc icu-io icu-i18n readline libxml-2.0  libxslt libzstd liblz4 "
            PACKAGES="\$PACKAGES lber ldap gmp odbc  odbccr  odbcinst libargon2  "

            CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES )" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) {$ldflags} " \
            LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) {$libs} " \
            ./configure  \
            --prefix={$pgsql_prefix} \
            --enable-coverage=no \
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
            --with-ldap \
            --without-bonjour \
            --without-tcl


            make -C src/bin/pg_config install

            make -C src/include install

            make -C  src/common install

            make -C  src/port install

            make -C  src/interfaces/libpq install


EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$pgsql_prefix}/lib/*.so.*
            rm -rf {$pgsql_prefix}/lib/*.so
            rm -rf {$pgsql_prefix}/lib/*.dylib
            rm -rf {$pgsql_prefix}/lib/libpgcommon_shlib.a
            rm -rf {$pgsql_prefix}/lib/libpgport_shlib.a

EOF
            )
            ->withPkgName('libpq')
            ->withBinPath($pgsql_prefix . '/bin/')
            ->withDependentLibraries(
                'zlib',
                'icu',
                'libxml2',
                'openssl',
                'readline',
                'libxslt',
                'libzstd',
                'liblz4',
                'libedit',
                'ncurses',
                'openldap',
                // 'ossp_uuid'
                // 'util_linux',
            )
    );
    $p->withExportVariable('LIBPQ_CFLAGS', '$(pkg-config  --cflags --static libpq)');
    $p->withExportVariable('LIBPQ_LIBS', '$(pkg-config    --libs   --static libpq)');
};

/*

    cd src/common && make -s -j$(nproc) all && make -s install && cd ../.. && \
    cd src/port && make -s -j$(nproc) all && make -s install && cd ../.. && \
    cd src/interfaces/libpq make -s -j$(nproc) all-static-lib && make -s install install-lib-static && \
    cd ../../bin/pg_config && make -j $(nproc) && make install && \

 */

/*
option('uuid', type: 'combo', choices: ['none', 'bsd', 'e2fs', 'ossp'],
*/
