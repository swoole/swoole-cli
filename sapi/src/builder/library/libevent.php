<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libevent_prefix = LIBEVENT_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $url = 'https://github.com/libevent/libevent/releases/download/release-2.1.12-stable/libevent-2.1.12-stable.tar.gz';
    $p->addLibrary(
        (new Library('libevent'))
            ->withHomePage('https://github.com/libevent/libevent')
            ->withLicense('https://github.com/libevent/libevent/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withManual('https://libevent.org/libevent-book/')
            ->withUrl($url)
            ->withPrefix($libevent_prefix)
            ->withConfigure(
                <<<EOF
                # 查看更多选项
                # cmake -LAH .
                mkdir -p build
                cd build
                cmake ..   \
                -DCMAKE_INSTALL_PREFIX={$libevent_prefix} \
                -DEVENT__DISABLE_DEBUG_MODE=ON \
                -DCMAKE_BUILD_TYPE=Release \
                -DEVENT__LIBRARY_TYPE=STATIC \
                -DEVENT__DISABLE_OPENSSL=OFF \
                -DEVENT__DISABLE_THREAD_SUPPORT=OFF \
                -DOpenSSL_ROOT={$openssl_prefix}

EOF
            )
            ->withPkgName('libevent')
            ->withPkgName('libevent_core')
            ->withPkgName('libevent_extra')
            ->withPkgName('libevent_openssl')
            ->withPkgName(' libevent_pthreads')
            ->withDependentLibraries('openssl')
            ->withBinPath($libevent_prefix . '/bin/')
    );
};
