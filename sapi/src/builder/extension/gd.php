<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $options = '--enable-gd --with-jpeg --with-freetype --with-webp --with-avif ';
    $dependent_libraries = ['libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif', 'libavif'];

    $p->addExtension(
        (new Extension('gd'))
            ->withHomePage('https://www.php.net/manual/zh/book.image.php')
            ->withOptions($options)
            ->withDependentLibraries(... $dependent_libraries)
    );
    $p->withExportVariable('FREETYPE2_CFLAGS', '$(pkg-config  --cflags --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
    $p->withExportVariable('FREETYPE2_LIBS', '$(pkg-config    --libs   --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
    if ($p->getOsType() == 'macos') {
        $libcpp = '-lc++';
    } else {
        $libcpp = '-lstdc++';
    }
    $p->withExportVariable('AVIF_CFLAGS', '$(pkg-config  --cflags --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc SvtAv1Dec aom dav1d libgav1)');
    $p->withExportVariable('AVIF_LIBS', '$(pkg-config    --libs   --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc SvtAv1Dec aom dav1d libgav1) ' . $libcpp);
};
