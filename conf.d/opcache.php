<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('opcache'))
            ->withHomePage('https://www.php.net/opcache')
            ->withOptions('--enable-opcache')
    );
};
