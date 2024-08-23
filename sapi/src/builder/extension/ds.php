<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('ds'))
            ->withOptions(' --enable-ds')
            ->withPeclVersion('1.4.0')
            ->withFileHash('md5', 'd7e64fbb53b567908d63155ec2a8f548')
            ->withHomePage('https://github.com/php-ds/ext-ds')
            ->withManual('https://www.php.net/ds')
            ->withLicense('https://github.com/php-ds/ext-ds/blob/master/LICENSE', Extension::LICENSE_MIT)
    );
};
