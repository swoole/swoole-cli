<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('gd'))
            ->withHomePage('https://www.php.net/manual/zh/book.image.php')
            ->withOptions('--enable-gd  --with-avif ') //  --with-jpeg --with-freetype --with-webp
            ->withDependentLibraries('libavif') //'libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif',
    );
};
