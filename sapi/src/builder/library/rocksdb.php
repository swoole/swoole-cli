<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $rocksdb_prefix = ROCKSDB_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $liburing_prefix = LIBURING_PREFIX;
    $p->addLibrary(
        (new Library('rocksdb'))
            ->withHomePage('http://rocksdb.org/')
            ->withLicense('https://github.com/facebook/rocksdb/blob/main/LICENSE.Apache', Library::LICENSE_APACHE2)
            ->withManual('https://github.com/facebook/rocksdb/blob/main/INSTALL.md')
            ->withUrl('https://github.com/facebook/rocksdb/archive/refs/tags/v8.1.1.tar.gz')
            ->withPrefix($rocksdb_prefix)
            ->withFile('rocksdb-v8.1.1.tar.gz')
            ->withConfigure(
                <<<EOF
                mkdir -p build
                cd build
                PACKAGES="zlib liblz4 libzstd  gflags liburing"
                CPPFLAGS="$(pkg-config  --cflags-only-I --static \$PACKAGES ) -I{$bzip2_prefix}/include " \
                LDFLAGS="$(pkg-config   --libs-only-L   --static \$PACKAGES ) -L{$bzip2_prefix}/lib " \
                LIBS="$(pkg-config      --libs-only-l   --static \$PACKAGES ) -lbz2" \
                cmake .. \
                -DCMAKE_INSTALL_PREFIX={$rocksdb_prefix} \
                -DCMAKE_BUILD_TYPE=Release  \
                -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
                -DBUILD_SHARED_LIBS=OFF \
                -DBUILD_STATIC_LIBS=ON \
                -During_ROOT={$liburing_prefix} \
                -DWITH_TESTS=OFF  \
                -DWITH_BENCHMARK_TOOLS=OFF \
                -DUSE_RTTI=ON \
                -DROCKSDB_BUILD_SHARED=OFF

EOF
            )
            ->withDependentLibraries(
                'zlib',
                'liblz4',
                'bzip2',
                'libzstd',
                'gflags',
                'liburing'
            )
        ->withPkgName('rocksdb')
    );
};
