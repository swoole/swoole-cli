<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('zstd'))
            ->withOptions('--enable-zstd')
            ->withPeclVersion('0.12.3')
            ->withHomePage('https://github.com/kjdev/php-ext-zstd.git')
            ->withLicense('https://github.com/kjdev/php-ext-zstd/blob/master/LICENSE', Extension::LICENSE_MIT)
            ->depends('libzstd')
    );
};
