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
        ->withFile('musl-cross-make-latest.tar.gz')
        ->withDownloadScript(
            'musl-cross-make',
            <<<EOF
            git clone -b master  --depth=1 https://github.com/richfelker/musl-cross-make.git

EOF
        )
        ->withPrefix($musl_cross_make_prefix)
        ->withBuildLibraryHttpProxy()
        //->withBuildCached(false)
        ->withConfigure(<<<EOF
        cp -f {$p->getWorkDir()}/sapi/musl-cross-make/config.mak .

EOF
        )
        ->withBinPath($musl_cross_make_prefix . '/bin/:' . $musl_cross_make_prefix . '/x86_64-linux-musl/bin/');

    $p->addLibrary($lib);

};
