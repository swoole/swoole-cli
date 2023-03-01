<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('swow'))
            ->withOptions('--enable-swow  --enable-swow-ssl --enable-swow-curl --enable-swow-memory-sanitizer --enable-swow-address-sanitizer --enable-swow-undefined-sanitizer')
            ->withHomePage('https://github.com/swow/swow')
            ->withLicense('https://github.com/swow/swow/blob/develop/LICENSE', Extension::LICENSE_APACHE2)
    );

};
