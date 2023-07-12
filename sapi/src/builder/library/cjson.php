<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $cjson_prefix = CJSON_PREFIX;
    $p->addLibrary(
        (new Library('cjson'))
            ->withHomePage('https://github.com/DaveGamble/cJSON.git')
            ->withLicense('https://github.com/DaveGamble/cJSON/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withManual('https://github.com/DaveGamble/cJSON#building')
            //->withUrl('https://github.com/DaveGamble/cJSON/archive/refs/tags/v1.7.16.tar.gz')
            //->withFile('cjson-v1.7.16.tar.gz')

            ->withFile('cjson-v1.7.15.tar.gz')
            ->withDownloadScript(
                'cJSON',
                <<<EOF
                git clone -b v1.7.15 https://github.com/taosdata-contrib/cJSON.git
EOF
            )

            ->withPrefix($cjson_prefix)
            ->withConfigure(
                <<<EOF
                mkdir -p build
                cd build
                cmake .. \
                -DCMAKE_INSTALL_PREFIX={$cjson_prefix} \
                -DCMAKE_C_STANDARD=11 \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_STATIC_LIBS=ON \
                -DBUILD_SHARED_LIBS=OFF \
                -DBUILD_SHARED_AND_STATIC_LIBS=ON \
                -DENABLE_CJSON_TEST=OFF \
                -DENABLE_CJSON_UTILS=ON \
                -DENABLE_TARGET_EXPORT=ON


EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$cjson_prefix}/lib/*.so.*
            rm -rf {$cjson_prefix}/lib/*.so
            rm -rf {$cjson_prefix}/lib/*.dylib
EOF
            )
        ->withPkgName('libcjson')
        ->withPkgName('libcjson_utils')
    );
};
