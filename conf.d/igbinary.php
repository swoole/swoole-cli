<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('igbinary'))
            ->withOptions('--enable-igbinary')
            ->withPeclVersion('3.2.14')
            ->withHomePage('https://www.php.net/igbinary')
            ->withLicense('https://github.com/phpredis/phpredis/blob/develop/COPYING', Extension::LICENSE_PHP)
    );
};
