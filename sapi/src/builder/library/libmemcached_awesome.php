<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmemcached_awesome_prefix = LIBMEMCACHED_AWESOME_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $lib = new Library('libmemcached_awesome');
    $lib->withHomePage('https://github.com/awesomized/libmemcached')
        ->withLicense('https://github.com/awesomized/libmemcached/blob/v1.x/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://awesomized.github.io/libmemcached/')
        ->withUrl('https://github.com/awesomized/libmemcached/archive/refs/tags/1.1.4.tar.gz')
        ->withFile('libmemcached-awesome-1.1.4.tar.gz')
        ->withPrefix($libmemcached_awesome_prefix)
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$libmemcached_awesome_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DENABLE_SASL=OFF \
        -DENABLE_DTRACE=OFF \
        -DENABLE_OPENSSL_CRYPTO=OFF \
        -DCMAKE_PREFIX_PATH="{$openssl_prefix}" \
        -DBUILD_TESTING=OFF \
        -DBUILD_DOCS=OFF \
        -DENABLE_MEMASLAP=OFF


        cmake --build . --config Release --target install

EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            sed -i.bak 's/-lmemcachedutil/-lmemcachedutil -lhashkit /' {$libmemcached_awesome_prefix}/lib/pkgconfig/libmemcached.pc

EOF
        )
        ->withPkgName('libmemcached');

    $p->addLibrary($lib);

    $libs = $p->isMacos() ? '-lc++' : ' -lstdc++ ';
    $p->withVariable('LIBS', '$LIBS ' . $libs);
};
