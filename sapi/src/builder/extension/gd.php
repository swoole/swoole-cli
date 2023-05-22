<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('gd'))
            ->withManual('https://www.php.net/manual/zh/image.installation.php')
            ->withHomePage('https://www.php.net/manual/zh/book.image.php')
            ->withOptions('--enable-gd --with-jpeg --with-freetype --with-webp')
            ->depends('libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif')
    );
};
