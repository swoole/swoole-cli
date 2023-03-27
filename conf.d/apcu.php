<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('apcu'))
        ->withOptions('--enable-apcu')
        ->withPeclVersion('5.1.22')
        ->withHomePage('https://pecl.php.net/package/APCu')
        ->withManual("https://github.com/krakjoe/apcu")
    );
};
