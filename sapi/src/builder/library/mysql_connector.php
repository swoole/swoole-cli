<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmysqlclient_prefix = LIBMYSQLICLIENT_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $ncurses_prefix = NCURSES_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $libedit_prefix = LIBEDIT_PREFIX;
    $libevent_prefix = LIBEVENT_PREFIX;
    $liblz4_prefix = LIBLZ4_PREFIX;
    $curl_prefix = CURL_PREFIX;
    $libfido2_prefix = LIBFIDO2_PREFIX;
    $protobuf_prefix = PROTOBUF_PREFIX;

    $lib = new Library('mysql_connector');
    $lib->withHomePage('http://www.mysql.com/')
        ->withLicense('https://github.com/mysql/mysql-server/blob/trunk/LICENSE', Library::LICENSE_SPEC)
        ->withManual('https://dev.mysql.com/doc/c-api/5.7/en/c-api-building-clients.html')
        ->withManual('https://dev.mysql.com/doc/refman/8.0/en/source-installation.html')
        ->withUrl('https://downloads.mysql.com/archives/get/p/20/file/mysql-connector-c%2B%2B-8.1.0-src.tar.gz')
        ->withFile('mysql-connector-8.1.0-src.tar.gz')
        ->withBuildCached(false)
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

        # sed -i '191 s@#@ @' {$p->getBuildDir()}/mysql_connector/cdk/cmake/DepFindSSL.cmake

        # sed -i '194 s@([a-z]|)[\t \\-]@@' {$p->getBuildDir()}/mysql_connector/cdk/cmake/DepFindSSL.cmake

        # sed -i '195 s@\\\\4@q@' {$p->getBuildDir()}/mysql_connector/cdk/cmake/DepFindSSL.cmake

        cp -f /work/DepFindSSL.cmake {$p->getBuildDir()}/mysql_connector/cdk/cmake/DepFindSSL.cmake

        cmake .. \
        -DCMAKE_INSTALL_PREFIX={$libmysqlclient_prefix} \
        -DCMAKE_INSTALL_LIBDIR={$libmysqlclient_prefix}/lib \
        -DCMAKE_INSTALL_INCLUDEDIR={$libmysqlclient_prefix}/include \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DBUILD_STATIC=ON \
        -DWITH_SSL=system \
        -DWITH_ZLIB=system \
        -DWITH_LZ4=system \
        -DWITH_ZSTD=system \
        -DWITH_JDBC=OFF \
        -DWITH_PROTOBUF=system \
        -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$zlib_prefix};{$ncurses_prefix};{$libzstd_prefix};{$libedit_prefix};{$libevent_prefix};{$liblz4_prefix};{$curl_prefix};{$libfido2_prefix};{$protobuf_prefix}" \

        cmake --build . --config Release

        cmake --build . --config Release --target install
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
            //'libfido2',
            'protobuf'
        );

    $p->addLibrary($lib);
};
