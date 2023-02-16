<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('openssl'))
            ->withOptions('--with-openssl --with-openssl-dir=/usr/openssl')
    );
};
