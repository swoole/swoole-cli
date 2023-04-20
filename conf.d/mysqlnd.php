<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('mysqlnd'))
            ->withHomePage('https://www.php.net/mysqlnd')
            ->withOptions('--enable-mysqlnd')
    );
};
