<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('pdo_pgsql'))
            ->withHomePage('https://www.php.net/pdo_pgsql')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withUrl('https://github.com/php/php-src.git ')
            ->withOptions('--with-pdo-pgsql=' . PGSQL_PREFIX)->depends('pgsql')
            ->withDependExtension('pdo')
    );
};
