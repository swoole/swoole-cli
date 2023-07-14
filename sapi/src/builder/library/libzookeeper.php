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
            ->withUrl('https://dlcdn.apache.org/zookeeper/zookeeper-3.8.1/apache-zookeeper-3.8.1.tar.gz')
            ->withManual('https://zookeeper.apache.org/doc/r3.8.1/zookeeperStarted.html')
            ->withManual('https://github.com/apache/zookeeper/blob/master/zookeeper-client/zookeeper-client-c/README')
            ->withConfigure(
                <<<EOF
            # apk add openjdk17

            test -f apache-ant-1.9.16-bin.zip || wget https://dlcdn.apache.org/ant/binaries/apache-ant-1.9.16-bin.zip
            test -d apache-ant && rm -rf apache-ant
            unzip -d apache-ant apache-ant-1.9.16-bin.zip

            test -f apache-maven-3.9.3-bin.zip || wget https://mirrors.cnnic.cn/apache/maven/maven-3/3.9.3/binaries/apache-maven-3.9.3-bin.zip
            test  -d maven && rm -rf maven
            unzip -d maven apache-maven-3.9.3-bin.zip

            export PATH={$build_dir}/libzookeeper/apache-ant/apache-ant-1.9.16/bin/:{$build_dir}/libzookeeper/maven/apache-maven-3.9.3/bin/:\$PATH

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

:<<=======EOF=======
            mkdir -p build
            cd build
            cmake .. \
            -DCMAKE_INSTALL_PREFIX="{$libzookeeper_prefix}" \
            -DCMAKE_BUILD_TYPE=Release \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DWANT_CPPUNIT=OFF \
            -DWITH_OPENSSL=ON  \
            -DOpenSSL_ROOT={$openssl_prefix} \
            -DBUILD_SHARED_LIBS=OFF \
            -DWITH_CYRUS_SASL=OFF
=======EOF=======


EOF
            )
        ->withBuildCached(false)
        ->withBinPath($libzookeeper_prefix . '/bin/')
    );
};
