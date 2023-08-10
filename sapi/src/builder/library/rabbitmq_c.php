<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $rabbitmq_c_prefix = RABBITMQ_C_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('rabbitmq_c');
    $lib->withHomePage('https://github.com/alanxz/rabbitmq-c.git')
        ->withLicense('https://github.com/alanxz/rabbitmq-c.git', Library::LICENSE_SPEC)
        ->withManual('https://github.com/alanxz/rabbitmq-c.git')
        ->withUrl('https://github.com/alanxz/rabbitmq-c/archive/refs/tags/v0.13.0.tar.gz')
        ->withPrefix($rabbitmq_c_prefix)
        ->withBuildScript(
            <<<EOF

            mkdir -p build
             cd build
             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$rabbitmq_c_prefix} \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DOpenSSL_ROOT={$openssl_prefix} \
            -DBUILD_EXAMPLES=OFF  \
            -DBUILD_TESTING=OFF \
            -DBUILD_TOOLS_DOCS=OFF \
            -DENABLE_SSL_SUPPORT=ON \
            -DBUILD_API_DOCS=OFF \
            -DRUN_SYSTEM_TESTS=OFF


            cmake --build . --config Release --target install

            sed -i.backup 's/-l -lssl/-lssl/g' {$rabbitmq_c_prefix}/lib/pkgconfig/librabbitmq.pc

EOF
        )
        ->withPkgName('librabbitmq');
    $p->addLibrary($lib);
};
