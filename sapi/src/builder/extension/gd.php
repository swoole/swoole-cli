<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('gd'))
            ->withHomePage('https://www.php.net/manual/zh/book.image.php')
            ->withOptions('--enable-gd --with-jpeg  --with-webp  --with-avif ') // --with-freetype
            ->withDependentLibraries('libavif', 'zlib','libjpeg', 'libwebp', 'libpng', 'libgif',) // 'freetype',
    );
};
