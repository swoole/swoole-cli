<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $options = '--enable-gd --with-jpeg --with-freetype --with-webp ';
    $dependent_libraries = ['libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif'];

    $options = '--enable-gd --with-jpeg --with-freetype --with-webp';

    $p->addExtension(
        (new Extension('gd'))
            ->withHomePage('https://www.php.net/manual/zh/book.image.php')
            ->withOptions($options)
            ->withDependentLibraries(... $dependent_libraries)
    );
    $p->withExportVariable('FREETYPE2_CFLAGS', '$(pkg-config  --cflags --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
    $p->withExportVariable('FREETYPE2_LIBS', '$(pkg-config    --libs   --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
};
