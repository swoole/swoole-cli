<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libks_prefix = LIBKS_PREFIX;

    $openssl_prefix = OPENSSL_PREFIX;
    $libuuid_prefix = LIBUUID_PREFIX;

    $lib = new Library('libks');
    $lib->withHomePage('https://github.com/signalwire/libks.git')
        ->withLicense('https://github.com/signalwire/libks.git', Library::LICENSE_SPEC)
        ->withManual('https://github.com/signalwire/libks.git')
        ->withFile('libks-2.0.2.tar.gz')
        ->withDownloadScript(
            'libks',
            <<<EOF
            git clone -b v2.0.2 --depth 1 --progress  https://github.com/signalwire/libks.git
EOF
        )
        ->withPrefix($libks_prefix)
        ->withBuildLibraryCached(false)
        ->withCleanBuildDirectory()
        ->withPreInstallCommand(
            'debian',
            <<<EOF
        apt-get install -y lsb-release
EOF
        )
        ->withConfigure(
            <<<EOF
            mkdir -p build
            cd build
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libks_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
            -DUUID_ROOT={$libuuid_prefix} \
            -DOpenSSL_ROOT={$openssl_prefix} \
            -DKS_STATIC=ON \
            -DCMAKE_OS_NAME="Debian"


EOF
        )
        ->withDependentLibraries('openssl', 'libuuid') //,'libatomic'
        ->withBinPath($libks_prefix . '/bin/');

    $p->addLibrary($lib);
};
