<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libevent_prefix = LIBEVENT_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $url = 'https://github.com/libevent/libevent/releases/download/release-2.1.12-stable/libevent-2.1.12-stable.tar.gz';
    $p->addLibrary(
        (new Library('libevent'))
            ->withHomePage('https://github.com/libevent/libevent')
            ->withLicense('https://github.com/libevent/libevent/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withManual('https://libevent.org/libevent-book/')
            //->withUrl($url)
            ->withFile('libevent-latest.tar.gz')
            ->withDownloadScript(
                'libevent',
                <<<EOF
        git clone -b master --depth=1  https://github.com/libevent/libevent.git
EOF
            )
            ->withPrefix($libevent_prefix)
            //->withBuildLibraryCached(false)
            ->withConfigure(
                <<<EOF
                mkdir -p build
                cd build
                # OPENSSL_LIBRARIES="{$openssl_prefix}/lib/" \
                cmake ..   \
                -DCMAKE_INSTALL_PREFIX={$libevent_prefix} \
                -DEVENT__DISABLE_DEBUG_MODE=ON \
                -DCMAKE_BUILD_TYPE=Release \
                -DEVENT__LIBRARY_TYPE=STATIC \
                -DEVENT__DISABLE_OPENSSL=OFF \
                -DEVENT__DISABLE_THREAD_SUPPORT=OFF \
                -DCMAKE_DISABLE_FIND_PACKAGE_PythonInterp=ON \
                -DCMAKE_DISABLE_FIND_PACKAGE_MbedTLS=ON \
                -DCMAKE_PREFIX_PATH="{$openssl_prefix};{$zlib_prefix}" \
                -DEVENT__DISABLE_TESTS=ON

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            sed -i.backup "s/-Ldl/  /" {$libevent_prefix}/lib/pkgconfig/libevent_openssl.pc
EOF
            )
            ->withPkgName('libevent')
            ->withPkgName('libevent_core')
            ->withPkgName('libevent_extra')
            ->withPkgName('libevent_openssl')
            ->withPkgName('libevent_pthreads')
            ->withDependentLibraries('openssl', 'zlib')
            ->withBinPath($libevent_prefix . '/bin/')
    );
};
