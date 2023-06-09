<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $coturn_prefix = COTURN_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    //$openssl_prefix = OPENSSL_v1_PREFIX;
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
            ->withConfigure(
                <<<EOF
           set -x
           test -d build  && rm -rf build
           mkdir -p build
           cd build
           cmake .. \
           -DCMAKE_INSTALL_PREFIX={$coturn_prefix} \
           -DCMAKE_BUILD_TYPE=Release \
           -DBUILD_SHARED_LIBS=OFF \
           -DOpenSSL_ROOT={$openssl_prefix} \
           -DLibevent_ROOT={$libevent_prefix} \
           -DSQLite_DIR={$sqlite3_prefix} \
           -DBUILD_STATIC_LIBS=ON \
           -DPostgreSQL_DIR={$pgsql_prefix} \

           #  hiredis
           # -Dhiredis_DIR={$sqlite3_prefix} \
EOF
            )
            ->withBinPath($coturn_prefix . '/bin/')
            ->depends('libevent', 'openssl', 'sqlite3', 'pgsql') # 'hiredis'
    );
};
