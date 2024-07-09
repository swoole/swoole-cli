<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('xlswriter'))
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->withPeclVersion('1.5.5')
            ->withFileHash('md5', '924847f19c20a6d071e91b7d2488021d')
            ->withOptions(' --with-xlswriter --enable-reader --with-openssl=' . OPENSSL_PREFIX)
            ->withDependentLibraries('openssl', 'zlib')
    );
};
