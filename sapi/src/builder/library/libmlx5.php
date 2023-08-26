<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmlx5_prefix = LIBMLX5_PREFIX;
    $libnl_prefix = LIBNL_PREFIX;
    $libdrm_prefix = LIBDRM_PREFIX;
    $lib = new Library('libmlx5');
    $lib->withHomePage('https://doc.dpdk.org/guides/platform/mlx5.html')
        ->withLicense('https://github.com/linux-rdma/rdma-core/blob/master/COPYING.GPL2', Library::LICENSE_GPL)
        ->withManual('https://doc.dpdk.org/guides/platform/mlx5.html')
        ->withFile('rdma-core-latest.tar.gz')
        ->withDownloadScript(
            'rdma-core',
            <<<EOF
            git clone -b master  --depth=1 https://github.com/linux-rdma/rdma-core.git
EOF
        )
        ->withPrefix($libmlx5_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libmlx5_prefix)
        ->withBuildLibraryCached(false)
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
             pip3 install cPython
EOF
        )
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build

             CFLAGS=-fPIC \
             cmake -GNinja .. \
            -DCMAKE_INSTALL_PREFIX={$libmlx5_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DENABLE_STATIC=1   \
            -DNO_PYVERBS=1   \
            -DNO_MAN_PAGES=1 \
            -DCMAKE_PREFIX_PATH="{$libnl_prefix};{$libdrm_prefix}" \
            -DCMAKE_DISABLE_FIND_PACKAGE_Systemd=ON \
            -DCMAKE_DISABLE_FIND_PACKAGE_libdrm=ON

            ninja
            ninja install

EOF
        )
        ->withPkgName('libmlx4')
        ->withPkgName('libibverbs')
        ->withPkgName('libmlx5')
        ->withPkgName('libefa')
        ->withPkgName('libibmad')
        ->withPkgName('libibnetdisc')
        ->withPkgName('libibumad')
        ->withPkgName('libmana')
        ->withPkgName('librdmacm')
        ->withBinPath($libmlx5_prefix . '/bin/:' . $libmlx5_prefix . '/sbin/')
        ->withDependentLibraries('libnl') //'libdrm','libudev'

    ;

    $p->addLibrary($lib);

};
