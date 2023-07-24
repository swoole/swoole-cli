<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $coturn_prefix = COTURN_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $libevent_prefix = LIBEVENT_PREFIX;
    $pgsql_prefix = PGSQL_PREFIX;
    $sqlite3_prefix = SQLITE3_PREFIX;
    $p->addLibrary(
        (new Library('coturn'))
            ->withHomePage('https://github.com/coturn/coturn/')
            ->withManual('https://github.com/coturn/coturn/tree/master/docs')
            ->withLicense('https://github.com/coturn/coturn/blob/master/LICENSE', Library::LICENSE_SPEC)
            ->withUrl('https://github.com/coturn/coturn/archive/refs/tags/docker/4.6.2-r1.tar.gz')
            ->withFile('coturn-v4.6.2.tar.gz')
            ->withDownloadScript(
                'coturn',
                <<<EOF
            git clone -b 4.6.2 --depth=1 https://github.com/coturn/coturn.git
EOF
            )
            ->withPrefix($coturn_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($coturn_prefix)
            ->withPreInstallCommand(
                <<<EOF

EOF
            )
            ->withBuildScript(
                <<<EOF
            PACKAGES='openssl libcrypto libssl  sqlite3'
            PACKAGES="\$PACKAGES libevent  libevent_core libevent_extra  libevent_openssl  libevent_pthreads"

            export CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)"
            export LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) -static"
            export LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)"
            ./configure --prefix=$coturn_prefix
            make -j {$p->maxJob}
            make install
EOF
            )
            ->withConfigure(
                <<<EOF
           test -d build  && rm -rf build
           mkdir -p build
           cd build

           cmake .. \
           -DCMAKE_INSTALL_PREFIX={$coturn_prefix} \
           -DCMAKE_C_STANDARD=C11 \
           -DCMAKE_C_FLAGS="-Werror -pedantic" \
           -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
           -DCMAKE_POLICY_DEFAULT_CMP0077=NEW \
           -DCMAKE_BUILD_TYPE=Release \
           -DBUILD_SHARED_LIBS=OFF \
           -DBUILD_STATIC_LIBS=ON \
           -DCMAKE_DISABLE_FIND_PACKAGE_mongo=ON \
           -DCMAKE_DISABLE_FIND_PACKAGE_hiredis=ON \
           -DCMAKE_DISABLE_FIND_PACKAGE_libsystemd=ON \
           -DCMAKE_DISABLE_FIND_PACKAGE_Prometheus=ON \
           -DCMAKE_DISABLE_FIND_PACKAGE_PostgreSQL=ON \
           -DCMAKE_DISABLE_FIND_PACKAGE_MySQL=ON \
           -DOpenSSL_ROOT={$openssl_prefix} \
           -DLibevent_ROOT={$libevent_prefix} \
           -DSQLite_DIR={$sqlite3_prefix} \
           -DOPENSSL_USE_STATIC_LIBS=ON \
           -DBUILD_TEST=OFF \
           -DFUZZER=OFF


           #-DPostgreSQL_DIR={$pgsql_prefix} \

           #  hiredis
           # -Dhiredis_DIR={$sqlite3_prefix} \

           cmake --build . --target install

EOF
            )
            ->withBinPath($coturn_prefix . '/bin/')
            ->withDependentLibraries('libevent', 'openssl', 'sqlite3') # 'hiredis' 'pgsql'
    );
};
