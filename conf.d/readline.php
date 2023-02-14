<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('ncurses',"/usr/ncurses/"))
            ->withUrl('https://ftp.gnu.org/pub/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withConfigure(<<<EOF
            mkdir -p /usr/ncurses/lib/pkgconfig
            ./configure \
            --prefix=/usr/ncurses \
            --enable-static \
            --disable-shared \
            --enable-pc-files \
            --with-pkg-config=/usr/ncurses/lib/pkgconfig \
            --with-pkg-config-libdir=/usr/ncurses/lib/pkgconfig \
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

            ->withScriptBeforeInstall('
            ln -s /usr/ncurses/lib/pkgconfig/formw.pc /usr/ncurses/lib/pkgconfig/form.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/menuw.pc /usr/ncurses/lib/pkgconfig/menu.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/ncurses++w.pc /usr/ncurses/lib/pkgconfig/ncurses++.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/ncursesw.pc /usr/ncurses/lib/pkgconfig/ncurses.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/panelw.pc /usr/ncurses/lib/pkgconfig/panel.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/ticw.pc /usr/ncurses/lib/pkgconfig/tic.pc ;

            ln -s /usr/ncurses/lib/libformw.a /usr/ncurses/lib/libform.a ;
            ln -s /usr/ncurses/lib/libmenuw.a /usr/ncurses/lib/libmenu.a ;
            ln -s /usr/ncurses/lib/libncurses++w.a /usr/ncurses/lib/libncurses++.a ;
            ln -s /usr/ncurses/lib/libncursesw.a /usr/ncurses/lib/libncurses.a ;
            ln -s /usr/ncurses/lib/libpanelw.a  /usr/ncurses/lib/libpanel.a ;
            ln -s /usr/ncurses/lib/libticw.a /usr/ncurses/lib/libtic.a ;
            ')
            ->withPkgName('ncursesw')
            ->withLicense('https://github.com/projectceladon/libncurses/blob/master/README', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/projectceladon/libncurses')
    );
    if (0) {
        $p->addLibrary(
            (new Library('libedit', '/usr/libedit'))
                ->withUrl('https://thrysoee.dk/editline/libedit-20210910-3.1.tar.gz')
                ->withConfigure('./configure --prefix=/usr/libedit --enable-static --disable-shared')
                ->withLdflags('')
                ->withLicense('http://www.netbsd.org/Goals/redistribution.html', Library::LICENSE_BSD)
                ->withHomePage('https://thrysoee.dk/editline/')
        );
    } else {
        $p->addLibrary(
            (new Library('readline', '/usr/readline'))
                ->withUrl('https://ftp.gnu.org/gnu/readline/readline-8.2.tar.gz')
                ->withConfigure(<<<EOF
                ./configure \
                --prefix=/usr/readline \
                --enable-static \
                --disable-shared \
                --with-curses \
                --enable-multibyte
EOF
                )
                ->withPkgName('readline')
                ->withLdflags('-L/usr/readline/lib')
                ->withLicense('http://www.gnu.org/licenses/gpl.html', Library::LICENSE_GPL)
                ->withHomePage('https://tiswww.case.edu/php/chet/readline/rltop.html')
                ->depends('ncurses')
        );
    }
    $p->addExtension((new Extension('readline'))
        ->withOptions('--with-readline=/usr/readline')
        ->depends('ncurses', 'readline')
    );
};
