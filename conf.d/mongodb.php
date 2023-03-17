<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('mongodb'))
            ->withOptions('--enable-mongodb --with-mongodb-system-libs=no --with-mongodb-ssl=openssl')
            ->withPeclVersion('1.14.2')
            ->depends('icu', 'openssl', 'zlib', 'libzstd')
    );
};
