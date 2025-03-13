<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('apcu'))
            ->withOptions('--enable-apcu')
            ->withPeclVersion('5.1.24')
            ->withFileHash('md5', '65494e2af7c92bdef075030b9d9e2da4')
            ->withHomePage('https://github.com/krakjoe/apcu.git')
            ->withManual('https://www.php.net/apcu')
            ->withLicense('https://github.com/krakjoe/apcu/blob/master/LICENSE', Extension::LICENSE_PHP)
    );
};
