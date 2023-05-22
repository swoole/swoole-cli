<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('posix'))
            ->withHomePage('https://www.php.net/posix')
            ->withOptions('--enable-posix')
    );
};
