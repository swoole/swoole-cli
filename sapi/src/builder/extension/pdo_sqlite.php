<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('pdo_sqlite'))
            ->withHomePage('https://www.php.net/pdo_sqlite')
            ->withOptions('--with-pdo-sqlite')
            ->withDependentLibraries('sqlite3')
            ->withDependentExtensions('pdo')
    );
};
