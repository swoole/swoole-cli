<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('mysqli'))
            ->withHomePage('https://www.php.net/mysqli')
            ->withOptions('--with-mysqli')
    );
};
