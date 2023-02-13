<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('ncurses'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withConfigure('./configure --prefix=/usr --enable-static --disable-shared')
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
                ->withConfigure('./configure --prefix=/usr/readline --enable-static --disable-shared')
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
