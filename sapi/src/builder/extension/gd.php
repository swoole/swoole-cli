<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $dependentLibraries = ['libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif'];

    $options = '--enable-gd --with-jpeg --with-freetype --with-webp  ';

    if ($p->getInputOption('with-libavif')) {
        $options .= '--with-avif';
        $dependentLibraries[] = 'libavif';

        $libyuv_prefix = LIBYUV_PREFIX;
        $p->withExportVariable('AVIF_CFLAGS', '$(pkg-config  --cflags --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc aom dav1d libgav1) ' . '-I' . $libyuv_prefix . '/include');
        $p->withExportVariable('AVIF_LIBS', '$(pkg-config    --libs   --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc aom dav1d libgav1) ' . '-L' . $libyuv_prefix . '/lib/ -lyuv ' . ($p->isMacos() ? ' -lc++ ' : ' -lstdc++ '));
    }

    $p->addExtension(
        (new Extension('gd'))
            ->withHomePage('https://www.php.net/manual/zh/book.image.php')
            ->withOptions($options)
            ->withDependentLibraries(... $dependentLibraries)
    );
    $p->withExportVariable('FREETYPE2_CFLAGS', '$(pkg-config  --cflags --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
    $p->withExportVariable('FREETYPE2_LIBS', '$(pkg-config    --libs   --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
};
