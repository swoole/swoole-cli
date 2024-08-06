<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $musl_libc_prefix = MUSL_LIBC_PREFIX;
    //文件名称 和 库名称一致
    $lib = new Library('musl_libc');
    $lib->withHomePage('https://opencv.org/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withManual('https://musl.libc.org/')
        ->withUrl('https://musl.libc.org/releases/musl-1.2.5.tar.gz')
        ->withPrefix($musl_libc_prefix)

        /* 使用 autoconfig automake  构建 start  */
        ->withConfigure(
            <<<EOF
        ./configure --help
        ./configure \
        --prefix={$musl_libc_prefix}
EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
        ln -sf /usr/include/linux/ {$musl_libc_prefix}/include/linux
        ln -sf /usr/include/x86_64-linux-gnu/asm/ {$musl_libc_prefix}/include/asm
        ln -sf /usr/include/asm-generic/ {$musl_libc_prefix}/include/asm-generic

EOF
        )

        ->withBinPath($musl_libc_prefix . '/bin/')
    ;
    $p->addLibrary($lib);

};
