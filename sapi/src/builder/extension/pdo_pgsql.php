<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $php_version = BUILD_PHP_VERSION;
    $p->addExtension(
        (new Extension('pdo_pgsql'))
            ->withHomePage('https://www.php.net/pdo_pgsql')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withOptions('--with-pdo-pgsql=' . PGSQL_PREFIX)
            ->withDependentExtensions('pdo')
            ->withDependentLibraries('pgsql')
    );
};
