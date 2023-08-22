<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $leveldb_prefix = LEVELDB_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $snappy_prefix = SNAPPY_PREFIX;
    $lib = new Library('leveldb');
    $lib->withHomePage('https://github.com/google/leveldb.git')
        ->withLicense('https://github.com/google/leveldb/blob/main/LICENSE', Library::LICENSE_BSD)
        ->withManual('https://github.com/google/leveldb.git')
        ->withFile('leveldb-latest.tar.gz')
        ->withDownloadScript(
            'leveldb',
            <<<EOF
                git clone -b main  --depth=1 https://github.com/google/leveldb.git
EOF
        )

        ->withPrefix($leveldb_prefix)
        ->withBuildScript(
            <<<EOF
             mkdir -p build
             cd build

             cmake .. \
            -DCMAKE_INSTALL_PREFIX={$leveldb_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON \
            -DLEVELDB_BUILD_TESTS=OFF \
            -DLEVELDB_BUILD_BENCHMARKS=OFF \
            -DLEVELDB_INSTALL=ON

            cmake --build . --config Release --target install

EOF
        )
    ;

    $p->addLibrary($lib);

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $leveldb_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $leveldb_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lleveldb');
};
