<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('1.2.0'))
        ->withUrl('https://github.com/yunnian/php-nsq')
        ->withManual('https://github.com/yunnian/php-nsq.git')
        ->withHomePage('https://github.com/yunnian/php-nsq.git')
        ->withLicense('https://github.com/yunnian/php-nsq/blob/master/LICENSE', Extension::LICENSE_PHP)
        ->withPeclVersion('3.5.1')
        ->depends('nsq', 'libevent')
    );
};
