<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('redis'))
        ->withOptions('--enable-redis')
        ->withPeclVersion('5.3.5')
        ->withHomePage('https://github.com/phpredis/phpredis')
        ->withLicense('https://github.com/phpredis/phpredis/blob/develop/COPYING', Extension::LICENSE_PHP)
    );
};
