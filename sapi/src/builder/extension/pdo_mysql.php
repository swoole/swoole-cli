<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('pdo_mysql'))
            ->withHomePage('https://www.php.net/pdo_mysql')
            ->withOptions('--with-pdo_mysql')
            ->withDependentExtensions('pdo', 'mysqlnd')
    );
};
