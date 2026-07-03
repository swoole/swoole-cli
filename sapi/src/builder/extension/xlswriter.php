<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('xlswriter'))
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->withPeclVersion('2.0.3')
            ->withFileHash('md5', '233f380e7fa2f74eb594946515fda1ad')
            ->withOptions(' --with-xlswriter --enable-reader --with-openssl=' . OPENSSL_PREFIX)
            ->withDependentLibraries('openssl')
    );
};
