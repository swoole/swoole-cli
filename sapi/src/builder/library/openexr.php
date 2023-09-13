<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $openexr_prefix = OPENEXR_PREFIX;

    $lib = new Library('openexr');
    $lib->withHomePage('https://openexr.com/en/latest/')
        ->withLicense('https://openexr.com/en/latest/license.html#license', Library::LICENSE_BSD)
        ->withManual('https://openexr.com/en/latest/install.html#install')
        ->withFile('openexr-latest.tar.gz')
        ->withDownloadScript(
            'openexr',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/AcademySoftwareFoundation/openexr.git
EOF
        )

        ->withPrefix($openexr_prefix)

        /* 使用 cmake 构建 start */
        ->withBuildScript(
            <<<EOF
        mkdir -p build
        cd build
        # cmake 查看选项
        # cmake -LH ..
        cmake .. \
        -DCMAKE_INSTALL_PREFIX={$openexr_prefix} \
        -DCMAKE_POLICY_DEFAULT_CMP0074=NEW \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON


        cmake --build . --config Release

        cmake --build . --config Release --target install

EOF
        )

        ->withPkgName('example')
        ->withBinPath($openexr_prefix . '/bin/')

        //依赖其它静态链接库
        ->withDependentLibraries('zlib', 'openssl')

    ;

    $p->addLibrary($lib);

};
