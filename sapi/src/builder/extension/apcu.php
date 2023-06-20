<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('apcu'))
            ->withOptions('--enable-apcu')
            ->withPeclVersion('5.1.22')
            ->withHomePage('https://github.com/krakjoe/apcu.git')
            ->withManual('https://www.php.net/apcu')
            ->withLicense('https://github.com/krakjoe/apcu/blob/master/LICENSE', Extension::LICENSE_PHP)
    );
};
