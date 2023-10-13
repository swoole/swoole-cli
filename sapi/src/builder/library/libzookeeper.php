<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $build_dir = $p->getBuildDir();
    $openssl_prefix = OPENSSL_PREFIX;
    $libzookeeper_prefix = LIBZOOKEEPER_PREFIX;
    $p->addLibrary(
        (new Library('libzookeeper'))
            ->withHomePage('https://zookeeper.apache.org/')
            ->withLicense('https://www.apache.org/licenses/', Library::LICENSE_APACHE2)
            //->withUrl('https://www.apache.org/dyn/closer.lua/zookeeper/zookeeper-3.8.2/apache-zookeeper-3.8.2.tar.gz')
            ->withUrl('https://dlcdn.apache.org/zookeeper/zookeeper-3.8.2/apache-zookeeper-3.8.2.tar.gz')
            ->withManual('https://zookeeper.apache.org/doc/r3.8.1/zookeeperStarted.html')
            ->withManual('https://github.com/apache/zookeeper/blob/master/zookeeper-client/zookeeper-client-c/README')
            //->withHttpProxy(false)
            //->withAutoUpdateFile()
            ->withPreInstallCommand(
                'alpine',
                <<<EOF
            apk add apache-ant maven

EOF
            )
            ->withBuildCached(false)
            ->withBuildLibraryHttpProxy()
            ->withPrefix($libzookeeper_prefix)
            // 自动清理构建目录
            ->withCleanBuildDirectory()

            // 自动清理安装目录
            ->withCleanPreInstallDirectory($libzookeeper_prefix)
            ->withConfigure(
                <<<EOF

            # ant compile_jute
            cd {$build_dir}/libzookeeper/zookeeper-jute/
            mvn compile

            cd {$build_dir}/libzookeeper/zookeeper-client/zookeeper-client-c
            ls -lha .

            autoreconf -if
            ./configure --help
            ./configure \
            --prefix={$libzookeeper_prefix} \
            --enable-shared=no \
            --enable-static=yes  \
            --with-openssl={$openssl_prefix} \
            --without-cppunit \
            --disable-doxygen-html \
            --without-sasl


EOF
            )
            /*
           ->withConfigure(<<<EOF
            cd {$build_dir}/libzookeeper/zookeeper-client/zookeeper-client-c
            mkdir -p build
            cd build
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libzookeeper_prefix} \
            -DCMAKE_BUILD_TYPE=Release \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DWANT_CPPUNIT=OFF \
            -DWITH_OPENSSL=ON  \
            -DOpenSSL_ROOT={$openssl_prefix} \
            -DBUILD_SHARED_LIBS=OFF \
            -DWITH_CYRUS_SASL=OFF

            cmake --build .
EOF
        )
            */
            ->withBinPath($libzookeeper_prefix . '/bin/')
    );
};
