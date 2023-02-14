<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('sqlite3',"/usr/sqlite3"))
            ->withUrl('https://www.sqlite.org/2021/sqlite-autoconf-3370000.tar.gz')
            ->withConfigure('./configure --prefix=/usr/sqlite3 --enable-static --disable-shared')
            ->withHomePage('https://www.sqlite.org/index.html')
            ->withLicense('https://www.sqlite.org/copyright.html', Library::LICENSE_SPEC)
    );
    $p->addExtension((new Extension('sqlite3'))->withOptions('--with-sqlite3=/usr/sqlite3')->depends('sqlite3'));
};
