<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $ngtcp2_prefix = NGTCP2_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $libnghttp3_prefix = NGHTTP3_PREFIX;
    $p->addLibrary(
        (new Library('ngtcp2'))
            ->withHomePage('https://github.com/ngtcp2/ngtcp2')
            ->withLicense('https://github.com/ngtcp2/ngtcp2/blob/main/COPYING', Library::LICENSE_MIT)
            ->withManual('https://curl.se/docs/http3.html')
            ->withUrl('https://github.com/ngtcp2/ngtcp2/releases/download/v1.17.0/ngtcp2-1.17.0.tar.gz')
            ->withFile('ngtcp2-1.17.0.tar.gz')
            ->withFileHash('md5', '7b5221830f1f09ea7998aaf7dfcb87ac')
            ->withPrefix($ngtcp2_prefix)
            ->withBuildScript(
                <<<EOF
             mkdir -p build
             cd build

             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$ngtcp2_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DENABLE_SHARED_LIB=OFF \
            -DENABLE_STATIC_LIB=ON \
            -DENABLE_OPENSSL=ON \
            -DENABLE_LIB_ONLY=ON \
            -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$libnghttp3_prefix}" \
            -DOPENSSL_ROOT_DIR={$openssl_prefix} \
            -DBUILD_TESTING=OFF

EOF
            )
            ->withPkgName('libngtcp2')
            ->withPkgName('libngtcp2_crypto_quictls')
            //->withPkgName('libngtcp2_crypto_openssl') # v1.0 版本 以后变更为 quictls
            ->withDependentLibraries('openssl', 'nghttp3')
    );
};
