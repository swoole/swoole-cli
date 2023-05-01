<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('session'))
            ->withHomePage('https://www.php.net/manual/zh/book.session.php')
            ->withOptions('--enable-session')
    );
};
