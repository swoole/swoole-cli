<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $unixODBC_prefix = UNIX_ODBC_PREFIX;
    $p->addExtension(
        (new Extension('pdo_odbc'))
            ->withHomePage('https://www.php.net/manual/zh/ref.pdo-odbc.php')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withOptions('--with-pdo-odbc=unixODBC,' . $unixODBC_prefix)
            ->withDependentLibraries('unix_odbc')
            ->withDependentExtensions('pdo')
    );
};
