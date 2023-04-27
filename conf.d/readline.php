<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $ncurses_prefix = NCURSES_PREFIX;
    $p->addLibrary(
        (new Library('ncurses'))
            ->withHomePage('https://invisible-island.net/ncurses/')
            ->withLicense('https://github.com/projectceladon/libncurses/blob/master/README', Library::LICENSE_MIT)
            ->withManual('https://invisible-island.net/ncurses/')
            ->withUrl('https://ftp.gnu.org/pub/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withMirrorUrl('https://mirrors.tuna.tsinghua.edu.cn/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withMirrorUrl('https://mirrors.ustc.edu.cn/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withPrefix($ncurses_prefix)
            ->withConfigure(
                <<<EOF
            mkdir -p {$ncurses_prefix}/lib/pkgconfig
            ./configure \
            --prefix={$ncurses_prefix} \
            --enable-static \
            --disable-shared \
            --enable-pc-files \
            --with-pkg-config={$ncurses_prefix}/lib/pkgconfig \
            --with-pkg-config-libdir={$ncurses_prefix}/lib/pkgconfig \
            --with-normal \
            --enable-widec \
            --enable-echo \
            --with-ticlib  \
            --without-termlib \
            --enable-sp-funcs \
            --enable-term-driver \
            --enable-ext-colors \
            --enable-ext-mouse \
            --enable-ext-putwin \
            --enable-no-padding \
            --without-debug \
            --without-tests \
            --without-dlsym \
            --without-debug \
            --enable-symlinks
EOF
            )
            ->withScriptBeforeInstall(
                '
                ln -sf ' . NCURSES_PREFIX . '/lib/pkgconfig/formw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/form.pc ;
                ln -sf ' . NCURSES_PREFIX . '/lib/pkgconfig/menuw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/menu.pc ;
                ln -sf ' . NCURSES_PREFIX . '/lib/pkgconfig/ncurses++w.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/ncurses++.pc ;
                ln -sf ' . NCURSES_PREFIX . '/lib/pkgconfig/ncursesw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/ncurses.pc ;
                ln -sf ' . NCURSES_PREFIX . '/lib/pkgconfig/panelw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/panel.pc ;
                ln -sf ' . NCURSES_PREFIX . '/lib/pkgconfig/ticw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/tic.pc ;

                ln -sf ' . NCURSES_PREFIX . '/lib/libformw.a ' . NCURSES_PREFIX . '/lib/libform.a ;
                ln -sf ' . NCURSES_PREFIX . '/lib/libmenuw.a ' . NCURSES_PREFIX . '/lib/libmenu.a ;
                ln -sf ' . NCURSES_PREFIX . '/lib/libncurses++w.a ' . NCURSES_PREFIX . '/lib/libncurses++.a ;
                ln -sf ' . NCURSES_PREFIX . '/lib/libncursesw.a ' . NCURSES_PREFIX . '/lib/libncurses.a ;
                ln -sf ' . NCURSES_PREFIX . '/lib/libpanelw.a  ' . NCURSES_PREFIX . '/lib/libpanel.a ;
                ln -sf ' . NCURSES_PREFIX . '/lib/libticw.a ' . NCURSES_PREFIX . '/lib/libtic.a ;
            '
            )
            ->withPkgName('ncursesw')
            ->withBinPath($ncurses_prefix . '/bin/')
    );
    if (0) {
        $p->addLibrary(
            (new Library('libedit'))
                ->withUrl('https://thrysoee.dk/editline/libedit-20210910-3.1.tar.gz')
                ->withPrefix(LIBEDIT_PREFIX)
                ->withConfigure('./configure --prefix=' . LIBEDIT_PREFIX . ' --enable-static --disable-shared')
                ->withLdflags('')
                ->withLicense('http://www.netbsd.org/Goals/redistribution.html', Library::LICENSE_BSD)
                ->withHomePage('https://thrysoee.dk/editline/')
        );
    } else {
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
                ->depends('ncurses')
        );
    }
    $p->addExtension(
        (new Extension('readline'))
            ->withHomePage('https://www.php.net/readline')
            ->withOptions('--with-readline=' . READLINE_PREFIX)
            ->depends('ncurses', 'readline')
    );
};
