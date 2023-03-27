<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('mcrypt'))
        ->withOptions('--with-mcrypt=' . LIBMCRYPT_PREFIX)
        ->withPeclVersion('1.0.5')
        ->withHomePage('https://github.com/php/pecl-encryption-mcrypt')
        ->withLicense('https://github.com/php/pecl-encryption-mcrypt/blob/master/LICENSE', Extension::LICENSE_PHP)
        ->depends('libmcrypt')
    );
};
