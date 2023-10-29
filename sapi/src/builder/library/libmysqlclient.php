<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmysqlclient_prefix = LIBMYSQLICLIENT_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $ncurses_prefix = NCURSES_PREFIX;
    $libzstd_prefix =LIBZSTD_PREFIX;
    $libedit_prefix = LIBEDIT_PREFIX;
    $libevent_prefix = LIBEVENT_PREFIX;
    $liblz4_prefix = LIBLZ4_PREFIX;
    $curl_prefix = CURL_PREFIX;
    $libfido2_prefix = LIBFIDO2_PREFIX;

    $lib = new Library('libmysqlclient');
    $lib->withHomePage('http://www.mysql.com/')
        ->withLicense('https://github.com/mysql/mysql-server/blob/trunk/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://dev.mysql.com/doc/c-api/5.7/en/c-api-building-clients.html')
        ->withManual('https://dev.mysql.com/doc/refman/8.0/en/source-installation.html')
        ->withFile('mysql-8.2.0.tar.gz')
        ->withDownloadScript(
            'mysql-server',
            <<<EOF
            git clone -b mysql-8.2.0 --depth=1 https://github.com/mysql/mysql-server.git
EOF
        )
        ->withBuildLibraryHttpProxy(true)
        //->withBuildCached(false)
            ->withPreInstallCommand('alpine', <<<EOF
            apk add libc6-compat
EOF
        )

        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$libmysqlclient_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DWITHOUT_SERVER=ON \
        -DDOWNLOAD_BOOST=1 \
        -DWITH_BOOST={$p->getBuildDir()}/libmysqlclient/boost/  \
        -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$zlib_prefix};{$ncurses_prefix};{$libzstd_prefix};{$libedit_prefix};{$libevent_prefix};{$liblz4_prefix};{$curl_prefix};{$libfido2_prefix}" \
        -DCURSES_INCLUDE_PATH={$ncurses_prefix}/include \
        -DEDITLINE_INCLUDE_PATH={$libedit_prefix}/include/editline \
        -DEDITLINE_ROOT={$libedit_prefix} \
        -DWITH_UNIT_TESTS=OFF \
        -DWITH_SYSTEM_LIBS=ON \
        -DWITH_EDITLINE=system \
        -DWITH_ZLIB=system \
        -DWITH_ZSTD=system \
        -DWITH_LZ4=system \
        -DWITH_CURL=system \
        -DWITH_FIDO=bundled \
        -DWITH_NDB=OFF

        make -j {$p->getMaxJob()} clientlib
        make -j {$p->getMaxJob()} mysqlclient
        make install

EOF
        )
        ->withDependentLibraries(
            'openssl',
            'zlib',
            'ncurses',
            'libzstd',
            //'numa',
            //openldap
            //kerberos
            'libedit',
            'libevent',
            'liblz4',
            'curl',
            //'libfido2'
        );

    $p->addLibrary($lib);
};
