<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $paho_mqtt_prefix = PAHO_MQTT_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('paho_mqtt');
    $lib->withHomePage('https://eclipse.dev/paho/index.php?page=clients/c/index.php')
        ->withLicense('https://github.com/eclipse/paho.mqtt.c/blob/master/CODE_OF_CONDUCT.md', Library::LICENSE_SPEC)
        ->withManual('https://eclipse.github.io/paho.mqtt.c/')
        ->withManual('https://eclipse.dev/paho/index.php?page=clients/c/index.php')
        ->withFile('paho.mqtt.c-v1.3.12.tar.gz')
        ->withDownloadScript(
            'paho.mqtt.c',
            <<<EOF
            git clone -b v1.3.12 https://github.com/eclipse/paho.mqtt.c.git
EOF
        )
        ->withPrefix($paho_mqtt_prefix)
        ->withConfigure(
            <<<EOF
             mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$paho_mqtt_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DPAHO_BUILD_SHARED=OFF\
            -DPAHO_BUILD_STATIC=ON \
            -DCMAKE_C_FLAGS="-fpic" \
            -DPAHO_WITH_SSL=ON \
            -DOpenSSL_ROOT={$openssl_prefix} \
            -DPAHO_BUILD_DOCUMENTATION=OFF \
            -DPAHO_BUILD_SAMPLES=OFF \
            -DPAHO_ENABLE_TESTING=OFF
EOF
        )
        ->withBuildCached(false)
        ->withPkgName('libsrtp2')
        ->withDependentLibraries('libpcap', 'openssl');

    $p->addLibrary($lib);
};
