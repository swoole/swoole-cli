<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $readline_prefix = READLINE_PREFIX;
    $p->addLibrary(
        (new Library('readline'))
            ->withHomePage('https://tiswww.case.edu/php/chet/readline/rltop.html')
            ->withLicense('https://www.gnu.org/licenses/gpl.html', Library::LICENSE_GPL)
            ->withUrl('https://ftp.gnu.org/gnu/readline/readline-8.2.tar.gz')
            ->withMirrorUrl('https://mirrors.tuna.tsinghua.edu.cn/gnu/readline/readline-8.2.tar.gz')
            ->withMirrorUrl('https://mirrors.ustc.edu.cn/gnu/readline/readline-8.2.tar.gz')
            ->withPrefix(READLINE_PREFIX)
            ->withConfigure(
                <<<EOF
                ./configure \
                --prefix={$readline_prefix} \
                --enable-static \
                --disable-shared \
                --with-curses \
                --enable-multibyte
EOF
            )
            ->withPkgName('readline')
            ->withLdflags('-L' . READLINE_PREFIX . '/lib')
            ->withDependentLibraries('ncurses')
    );
};
