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
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($coturn_prefix)
            ->withBuildScript(
                <<<EOF
           set -x
           test -d build  && rm -rf build
           mkdir -p build
           cd build
           #   -DCMAKE_MODULE_PATH="{$openssl_prefix}:{$openssl_prefix}"
           cmake .. \
           -DCMAKE_INSTALL_PREFIX={$coturn_prefix} \
           -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
           -DCMAKE_BUILD_TYPE=Release \
           -DBUILD_SHARED_LIBS=OFF \
           -DBUILD_STATIC_LIBS=ON \
           -DOpenSSL_ROOT={$openssl_prefix} \
           -DLibevent_ROOT={$libevent_prefix} \
           -DSQLite_DIR={$sqlite3_prefix}

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
