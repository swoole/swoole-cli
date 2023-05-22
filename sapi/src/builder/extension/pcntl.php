<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('pcntl'))
            ->withHomePage('https://www.php.net/pcntl')
            ->withOptions('--enable-pcntl')
    );
};
