<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('ds'))
            ->withOptions(' --enable-ds')
            ->withPeclVersion('1.6.0')
            ->withFileHash('md5', 'c743b75f58bedfa2ab7fb3853b7b629b')
            ->withHomePage('https://github.com/php-ds/ext-ds')
            ->withManual('https://www.php.net/ds')
            ->withLicense('https://github.com/php-ds/ext-ds/blob/master/LICENSE', Extension::LICENSE_MIT)
    );
};
