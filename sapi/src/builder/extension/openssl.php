<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('openssl'))
            ->withHomePage('https://www.php.net/openssl')
            ->withOptions('--with-openssl --with-openssl-dir=' . OPENSSL_PREFIX)
            ->withDependentLibraries('openssl')
    );
};
