<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $unixODBC_prefix = UNIX_ODBC_PREFIX;
    $p->addExtension(
        (new Extension('pdo_odbc'))
            ->withHomePage('https://www.php.net/manual/zh/ref.pdo-odbc.php')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withUrl('https://github.com/php/php-src.git ')
            ->withOptions('./configure --with-pdo-odbc=unixODBC,' . $unixODBC_prefix)
            ->withDependentLibraries('unixodbc')
            ->withDependentExtensions('pdo')
    );
};
