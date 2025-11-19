<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $nghttp2_prefix = NGHTTP2_PREFIX;
    $cares_prefix = CARES_PREFIX;
    $openssl_prefix =OPENSSL_PREFIX;
    $p->addLibrary(
        (new Library('nghttp2'))
            ->withHomePage('https://github.com/nghttp2/nghttp2.git')
            ->withManual('https://nghttp2.org/')
            ->withLicense('https://github.com/nghttp2/nghttp2/blob/master/COPYING', Library::LICENSE_MIT)
            ->withUrl('https://github.com/nghttp2/nghttp2/releases/download/v1.68.0/nghttp2-1.68.0.tar.gz')
            ->withFileHash('md5', 'e0d023d49a8d07d4d7ff8c5a93725720')
            ->withPrefix($nghttp2_prefix)
            ->withBuildScript(
                <<<EOF
             mkdir -p build
             cd build

             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$nghttp2_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF \
            -DBUILD_STATIC_LIBS=ON \
            -DENABLE_LIB_ONLY=ON \
            -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$cares_prefix}" \
            -DOPENSSL_ROOT_DIR={$openssl_prefix} \
            -DENABLE_APP=OFF \
            -DENABLE_DOC=OFF \
            -DWITH_LIBXML2=ON \
            -DBUILD_TESTING=OFF \
            -DCMAKE_DISABLE_FIND_PACKAGE_Libngtcp2=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_Systemd=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_Libngtcp2=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_Libnghttp3=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_Jansson=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_Jemalloc=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_Libevent=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_Python3=ON

            cmake --build . --target install
EOF
            )
            ->withPkgName('libnghttp2')
            ->withDependentLibraries('openssl', 'zlib', 'libxml2', 'cares')
    );
};
