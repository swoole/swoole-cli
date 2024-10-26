<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('gd'))
            ->withHomePage('https://www.php.net/manual/zh/book.image.php')
            ->withOptions('--enable-gd --with-jpeg --with-freetype --with-webp --with-avif ')
            ->withDependentLibraries('libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif', 'libavif')
    );

    $p->withExportVariable('FREETYPE2_CFLAGS', '$(pkg-config  --cflags --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
    $p->withExportVariable('FREETYPE2_LIBS', '$(pkg-config    --libs   --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');

    $p->withExportVariable('AVIF_CFLAGS', '$(pkg-config  --cflags --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc SvtAv1Dec aom dav1d libgav1)');
    $p->withExportVariable('AVIF_LIBS', '$(pkg-config    --libs   --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc SvtAv1Dec aom dav1d libgav1) ' . ($p->isMacos() ? '-lc++' : '-lstdc++'));

};
