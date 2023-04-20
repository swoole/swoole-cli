<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('pdo'))
            ->withHomePage('https://www.php.net/pdo')
            ->withOptions('--enable-pdo')
    );
};
