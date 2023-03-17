<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('gd'))
        ->withOptions('--enable-gd --with-jpeg --with-freetype --with-webp --with-jpeg')
        ->withManual('https://www.php.net/manual/zh/image.installation.php')
        ->depends('libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif')
    );
};
