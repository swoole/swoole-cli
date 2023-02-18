<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('zstd'))
            ->withOptions('--with-zstd')
            ->withHomePage('https://github.com/kjdev/php-ext-zstd')
            ->withLicense('http://github.com/libffi/libffi/blob/master/LICENSE', Extension::LICENSE_MIT)
            ->withPeclVersion('0.12.1')
    );
};
