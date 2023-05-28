<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('pgsql'))
            ->withHomePage('https://www.php.net/pgsql')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withUrl('https://github.com/php/php-src.git ')
            ->withOptions('--with-pgsql=' . PGSQL_PREFIX)->depends('pgsql')
    );
};
