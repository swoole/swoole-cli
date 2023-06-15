<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('sqlite3'))
            ->withHomePage(' https://www.php.net/sqlite3')
            ->withOptions('--with-sqlite3')
            ->withDependentLibraries('sqlite3')
    );
};
