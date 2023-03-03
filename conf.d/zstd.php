<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('zstd'))
            ->withOptions('--enable-zstd')
            ->withHomePage('https://github.com/kjdev/php-ext-zstd')
            ->withLicense('https://github.com/kjdev/php-ext-zstd/blob/master/LICENSE', Extension::LICENSE_MIT)
            ->withPeclVersion('0.12.1')
            ->depends('libzstd')
    );
};
