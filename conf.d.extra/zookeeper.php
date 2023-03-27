<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('1.2.0'))
        ->withUrl('https://github.com/php-zookeeper/php-zookeeper')
        ->withManual('https://github.com/php-zookeeper/php-zookeeper')
        ->withHomePage('https://github.com/php-zookeeper/php-zookeeper')
        ->withLicense('https://github.com/php-zookeeper/php-zookeeper/blob/master/LICENSE', Extension::LICENSE_PHP)
        //->withPeclVersion('1.2.0')
        ->depends('zookeeper')
    );
};
