<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('bcmath'))
            ->withHomePage('https://www.php.net/bc')
            ->withManual('https://www.php.net/manual/zh/book.bc.php')
            ->withOptions('--enable-bcmath')
    );
};
