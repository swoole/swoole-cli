<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $prometheus_client_c_prefix = PROMETHEUS_CLIENT_C_PREFIX;
    $p->addLibrary(
        (new Library('prometheus_client_c'))
            ->withHomePage('https://github.com/digitalocean/prometheus-client-c.git')
            ->withLicense('https://github.com/digitalocean/prometheus-client-c/blob/master/LICENSE', Library::LICENSE_APACHE2)
            ->withManual('https://digitalocean.github.io/prometheus-client-c/')
            ->withUrl('https://github.com/digitalocean/prometheus-client-c/archive/refs/tags/v0.1.3.tar.gz')
            ->withFile('prometheus-client-c-v0.1.3.tar.gz')
            ->withPrefix($prometheus_client_c_prefix)
            ->withBuildScript(
                <<<EOF
                cat > CMakeLists.txt <<PROMETHEUS_EOF
cmake_minimum_required(VERSION 3.14.5)
project(prometheus-client-c)

add_subdirectory(prom)
add_subdirectory(promhttp)

PROMETHEUS_EOF



         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$prometheus_client_c_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON

        # 或者
        # ./auto build
EOF
            )
            ->withBinPath($prometheus_client_c_prefix . '/bin/')
            ->withDependentLibraries(
                'openssl',
                'readline',
                //'libmicrohttpd'
            )
    );
};
