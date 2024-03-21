<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $unixODBC_prefix = UNIX_ODBC_PREFIX;
    $php_version = BUILD_PHP_VERSION;
    $p->addExtension(
        (new Extension('pdo_odbc'))
            ->withHomePage('https://www.php.net/manual/zh/ref.pdo-odbc.php')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withUrl('https://github.com/php/php-src.git ')
            ->withFile('pdo_odbc-' . $php_version . '.tgz')
            ->withDownloadScript(
                'pdo_odbc',
                <<<EOF
                git clone -b php-{$php_version} --depth=1 https://github.com/php/php-src.git
                cp -rf php-src/ext/pdo_odbc  pdo_odbc
EOF
            )
            ->withOptions('--with-pdo-odbc=unixODBC,' . $unixODBC_prefix)
            ->withDependentLibraries('unixODBC')
            ->withDependentExtensions('pdo')
    );
};
