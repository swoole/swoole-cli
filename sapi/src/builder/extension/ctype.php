<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('ctype'))
            ->withHomePage('https://www.php.net/ctype')
            ->withOptions('--enable-ctype')
    );
};
