<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('redis'))
            ->withOptions('--enable-redis  ')
            ->withPeclVersion('6.1.0')
            ->withFileHash('md5', 'ca6277b27ee35e1f55f9ad7f0b4df29b')
            ->withHomePage('https://github.com/phpredis/phpredis')
            ->withLicense('https://github.com/phpredis/phpredis/blob/develop/COPYING', Extension::LICENSE_PHP)
            ->withDependentExtensions('session')
    );
};
