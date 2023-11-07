<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $example_prefix = EXAMPLE_PREFIX;
    $libceph_prefix = LIBCEPH_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    //文件名称 和 库名称一致
    $lib = new Library('libceph');
    $lib->withHomePage('https://ceph.io/')
        ->withLicense('https://github.com/ceph/ceph/blob/main/COPYING-LGPL3', Library::LICENSE_LGPL)
        ->withManual('https://github.com/ceph/ceph')
        //->withAutoUpdateFile()
        ->withFile('ceph-latest.tar.gz')
        ->withDownloadScript(
            'ceph',
            <<<EOF
                # git clone -b main  --depth=1 --recursive https://github.com/ceph/ceph.git
                # for docs
                git clone -b main  --depth=1  https://github.com/ceph/ceph.git
EOF
        )
        ->withPrefix($libceph_prefix)

        ->withPreInstallCommand(
            'macos',
            <<<EOF
       brew install osxfuse
EOF
        )
        ->withPreInstallCommand(
            'alpine',
            <<<EOF
        apk add yasm
        pip3 install prettytable PyYAML phinx-doc
EOF
        )
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

        cmake .. \
        -DCMAKE_INSTALL_PREFIX={$libceph_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON  \
        -DDIAGNOSTICS_COLOR=always  \
        -DOPENSSL_ROOT_DIR={$openssl_prefix} \
        -DWITH_BABELTRACE=OFF  \
        -DWITH_BLUESTORE=OFF  \
        -DWITH_CCACHE=OFF  \
        -DWITH_CEPHFS=OFF  \
        -DWITH_KRBD=OFF  \
        -DWITH_LIBCEPHFS=ON  \
        -DWITH_LTTNG=OFF  \
        -DWITH_LZ4=OFF  \
        -DWITH_MANPAGE=ON  \
        -DWITH_MGR=OFF  \
        -DWITH_MGR_DASHBOARD_FRONTEND=OFF  \
        -DWITH_PYTHON3=3.11  \
        -DWITH_RADOSGW=OFF  \
        -DWITH_RDMA=OFF  \
        -DWITH_SPDK=OFF  \
        -DWITH_SYSTEM_BOOST=ON  \
        -DWITH_SYSTEMD=OFF  \
        -DWITH_TESTS=OFF  \
        -DWITH_XFS=OFF  \


        cmake --build . --config Release

        cmake --build . --config Release --target install


EOF
        )
        ->withBinPath($example_prefix . '/bin/')

        ->withDependentLibraries(
            'zlib',
            'openssl',
            'boost',
            'leveldb',
            // 'nss',
            // 'cython'
        )
    ;
    $p->addLibrary($lib);
};
