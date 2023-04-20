<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('filter'))
            ->withHomePage('http://www.php.net/filter')
            ->withOptions('--enable-filter')
    );
};
