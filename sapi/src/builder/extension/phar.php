<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('phar'))
            ->withHomePage('https://www.php.net/phar')
            ->withOptions('--enable-phar')
    );
};
