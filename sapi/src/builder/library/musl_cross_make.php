<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $musl_cross_make_prefix = MUSL_CROSS_MAKE_PREFIX;

    //文件名称 和 库名称一致
    $lib = new Library('musl_cross_make');
    $lib->withHomePage('https://github.com/richfelker/musl-cross-make.git')
        ->withLicense('https://github.com/richfelker/musl-cross-make?tab=MIT-1-ov-file#readme', Library::LICENSE_MIT)
        ->withManual('https://github.com/richfelker/musl-cross-make/blob/master/README.md')
        /* 下载依赖库源代码方式二 start */
        ->withFile('musl-cross-make-latest.tar.gz')
        ->withDownloadScript(
            'musl-cross-make',
            <<<EOF
                git clone -b master  --depth=1 https://github.com/richfelker/musl-cross-make.git
EOF
        )
        ->withPrefix($musl_cross_make_prefix)
        ->withBuildLibraryHttpProxy()

        /* 使用 autoconfig automake  构建 start  */
        ->withConfigure(
            <<<EOF
        TARGET=x86_64-linux-musl
        OUTPUT={$musl_cross_make_prefix}
        DL_CMD ="curl -C - -L -o"
EOF
        )
        ->withPkgName('example')
        ->withBinPath($musl_cross_make_prefix . '/bin/')

    ;

    $p->addLibrary($lib);

};
