<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $libgif_prefix = GIF_PREFIX;
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libgif_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libgif_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lgif');

    $p->addExtension(
        (new Extension('gd'))
            ->withOptions('--enable-gd --with-jpeg --with-freetype --with-webp ')
            ->withManual('https://www.php.net/manual/zh/image.installation.php')
            ->depends('libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif')
    );
};
