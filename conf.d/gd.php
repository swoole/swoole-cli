<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('gd'))
        ->withOptions('--enable-gd \
        --with-jpeg=/usr/libjpeg/ \
        --with-freetype=/usr/freetype \
        --with-webp=/usr/libwebp')
        ->withManual('https://www.php.net/manual/zh/image.installation.php')
    );
};
