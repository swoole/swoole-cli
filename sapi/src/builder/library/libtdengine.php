<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $tdengine_prefix = TDENGINE_PREFIX;
    $p->addLibrary(
        (new Library('libtdengine'))
            ->withHomePage('https://www.taosdata.com/')
            ->withLicense('https://github.com/taosdata/TDengine/blob/main/LICENSE', Library::LICENSE_SPEC)
            ->withManual('https://docs.taosdata.com/get-started/')
            ->withUrl('https://github.com/taosdata/TDengine/archive/refs/tags/ver-3.0.5.1.tar.gz')
            ->withPrefix($tdengine_prefix)
            ->withFile('TDengine-ver-3.0.5.1.tar.gz')
            ->withDownloadScript(
                'TDengine',
                <<<EOF
        git clone it clone --recurse -b ver-3.0.5.1 --depth=1 https://github.com/taosdata/TDengine.git
EOF
            )
            ->withBuildScript(
                <<<EOF
              mkdir -p build
              cd build
              cmake .. \
              -DBUILD_TOOLS=true \
              -DCMAKE_INSTALL_PREFIX={$tdengine_prefix} \
              -DCMAKE_BUILD_TYPE=Release  \
              -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
              -DBUILD_STATIC_LIBS=ON \


              make -j \${LOGICAL_PROCESSORS}
EOF
            )
    );
};
