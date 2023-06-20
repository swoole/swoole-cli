<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('pdo_odbc'))
            ->withOptions('--with-pdo-odbc=unixODBC,/usr/local')
            ->withHomePage('https://www.php.net/pdo_odbc')
            ->withDependentExtensions('pdo')
            ->withDependentLibraries('unixODBC')
    );
};
