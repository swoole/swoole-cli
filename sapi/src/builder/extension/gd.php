<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {

    $php_version_id = BUILD_CUSTOM_PHP_VERSION_ID;
    $file = '';
    $url = '';
    $download_dir_name = '';
    $download_script = '';

    $dependent_libraries = ['libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif'];
    $dependent_extensions = [];
    $options = '--enable-gd --with-jpeg --with-freetype --with-webp ';

    if ($php_version_id >= 8010) {
        if ($p->getInputOption('with-libavif')) {
            $options .= ' --with-avif ';
            $dependent_libraries[] = 'libavif';

            if ($p->getOsType() == 'macos') {
                $libcpp = '-lc++';
            } else {
                $libcpp = '-lstdc++';
            }
            $p->withExportVariable('AVIF_CFLAGS', '$(pkg-config  --cflags --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc SvtAv1Dec aom dav1d libgav1)');
            $p->withExportVariable('AVIF_LIBS', '$(pkg-config    --libs   --static libavif libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc SvtAv1Dec aom dav1d libgav1) ' . $libcpp);
        }
    }

    $ext = (new Extension('gd'))
        ->withHomePage('https://www.php.net/manual/zh/book.image.php')
        ->withOptions($options);
    call_user_func_array([$ext, 'withDependentLibraries'], $dependent_libraries);
    $p->addExtension($ext);

    $p->withExportVariable('FREETYPE2_CFLAGS', '$(pkg-config  --cflags --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
    $p->withExportVariable('FREETYPE2_LIBS', '$(pkg-config    --libs   --static  libbrotlicommon libbrotlidec libbrotlienc freetype2 zlib libpng)');
};