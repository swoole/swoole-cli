<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $sqlite3_prefix = SQLITE3_PREFIX;
    $p->addLibrary(
        (new Library('sqlite3'))
            ->withHomePage('https://www.sqlite.org/index.html')
            ->withLicense('https://www.sqlite.org/copyright.html', Library::LICENSE_SPEC)
            ->withManual('https://www.sqlite.org/docs.html')
            ->withUrl('https://www.sqlite.org/2021/sqlite-autoconf-3370000.tar.gz')
            ->withPrefix($sqlite3_prefix)
            ->withConfigure('./configure --prefix=' . $sqlite3_prefix . ' --enable-static --disable-shared')
            ->withBinPath($sqlite3_prefix)
            ->withPkgName('sqlite3')
    );
    $p->addExtension(
        (new Extension('sqlite3'))
            ->withHomePage(' https://www.php.net/sqlite3')
            ->withOptions('--with-sqlite3')
            ->depends('sqlite3')
    );
};
