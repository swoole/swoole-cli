<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('sockets'))
            ->withHomePage('https://www.php.net/sockets')
            ->withOptions('--enable-sockets')
    );
};
