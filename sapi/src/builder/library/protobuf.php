<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $protobuf_prefix = PROTOBUF_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('protobuf');
    $lib->withHomePage('protobuf.dev')
        ->withLicense('https://github.com/protocolbuffers/protobuf/blob/main/LICENSE', Library::LICENSE_LGPL)
        ->withManual('https://github.com/protocolbuffers/protobuf.git')
        ->withFile('protobuf-latest.tar.gz')
        ->withDownloadScript(
            'protobuf',
            <<<EOF
                git clone -b main  --depth=1 --recursive https://github.com/protocolbuffers/protobuf.git
EOF
        )
        ->withPrefix($protobuf_prefix)
        ->withBuildScript(
            <<<EOF
         mkdir -p build
         cd build

         cmake .. \
        -DCMAKE_INSTALL_PREFIX={$protobuf_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -Dprotobuf_BUILD_TESTS=OFF \
        -DCMAKE_PREFIX_PATH="{$zlib_prefix};" \
        -DABSL_PROPAGATE_CXX_STD=ON

        cmake --build . --config Release

        cmake --build . --config Release --target install

EOF
        )
        ->withPkgName('protobuf')
        ->withBinPath($protobuf_prefix . '/bin/')
        ->withDependentLibraries(
            'zlib'
        )
    ;

    $p->addLibrary($lib);
};
