<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $musl_cross_make_prefix = MUSL_CROSS_MAKE_PREFIX;

    $make_options = "TARGET='x86_64-linux-musl' ";
    $make_options .= "OUTPUT='{$musl_cross_make_prefix}' ";
    $make_options .= "DL_CMD='curl -C - -L -o' ";
    $make_options .= "GCC_VER='11.2.0' ";

    $make_common_options = 'CC="x86_64-linux-musl-gcc -static --static" ';
    $make_common_options .= 'CXX="x86_64-linux-musl-g++ -static --static" ';
    $make_common_options .= 'CFLAGS="-g0 -Os" ';
    $make_common_options .= 'CXXFLAGS="-g0 -Os" ';
    $make_common_options .= 'LDFLAGS="-s" ';

    $make_gcc_options = '--disable-libquadmath --disable-decimal-float ';
    $make_gcc_options .= '--disable-libitm ';
    $make_gcc_options .= '--disable-fixed-point ';
    $make_gcc_options .= '--enable-languages=c,c++ ';
    $make_gcc_options .= '--disable-nls ';

    $make_options = $make_options . "COMMON_CONFIG='{$make_common_options}' ";
    $make_options = $make_options . "GCC_CONFIG='{$make_gcc_options}' ";

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
