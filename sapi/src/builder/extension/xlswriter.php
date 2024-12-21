<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('xlswriter'))
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->withPeclVersion('1.5.8')
            ->withFileHash('md5', '3fe390104254a03d1cd8d59aacf252ef')
            ->withOptions(' --with-xlswriter --enable-reader --with-openssl=' . OPENSSL_PREFIX)
            ->withDependentLibraries('openssl', 'zlib')
    );
};
