<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $workDir = $p->getWorkDir();
    $openssl_prefix = OPENSSL_PREFIX;
    $zookeeper_prefix = ZOOKEEPER_PREFIX;
    $p->addLibrary(
        (new Library('libzookeeper'))
            ->withHomePage('https://zookeeper.apache.org/')
            ->withLicense('https://www.apache.org/licenses/', Library::LICENSE_APACHE2)
            ->withUrl('https://dlcdn.apache.org/zookeeper/zookeeper-3.8.1/apache-zookeeper-3.8.1.tar.gz')
            ->withManual('https://zookeeper.apache.org/doc/r3.8.1/zookeeperStarted.html')
            ->withBuildScript(
                <<<EOF
             ant compile_jute
            cd zookeeper-client/zookeeper-client-c
            cmake .
            -DCMAKE_BUILD_TYPE=Release \
            -DCMAKE_INSTALL_PREFIX="{$zookeeper_prefix}" \
            -DWANT_CPPUNIT=OFF \
            -DWITH_OPENSSL=ON  \
            -DBUILD_SHARED_LIBS=OFF

            cmake --build .
EOF
            )
            ->withConfigure(
                <<<EOF

            ant compile_jute
            cd zookeeper-client/zookeeper-client-c
            autoreconf -if
            ./configure --help

            ./configure \
            --prefix={$zookeeper_prefix} \
            --enable-shared=no \
            --enable-static=yes  \
            --with-openssl={$openssl_prefix} \
            --without-cppunit

EOF
            )
    );
};
