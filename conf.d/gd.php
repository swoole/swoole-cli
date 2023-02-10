<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

/**
 *  参考文档
 * https://www.php.net/manual/zh/image.installation.php
 */
return function (Preprocessor $p) {
    $p->addExtension((new Extension('gd'))
        ->withOptions('--enable-gd \
        --with-jpeg=/usr/libjpeg/ \
        --with-png-dir=/usr/libpng \
        --with-zlib-dir=/usr/zlib \
        --with-freetype=/usr/freetype \
        --with-webp=/usr/libwebp')
    );
};
