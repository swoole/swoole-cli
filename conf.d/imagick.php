<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $imagemagick_prefix = IMAGEMAGICK_PREFIX;
    $p->addLibrary(
        (new Library('imagemagick'))
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.0-62.tar.gz')
            ->withPrefix($imagemagick_prefix)
            ->withCleanBuildDirectory()
            ->withCleanInstallDirectory($imagemagick_prefix)
            ->withFile('ImageMagick-v7.1.0-62.tar.gz')
            ->withPrefix($imagemagick_prefix)
            ->withConfigure(<<<EOF
            ./configure --help   
            CPPFLAGS="$(pkg-config --cflags-only-I --static libzip zlib libzstd freetype2 libxml-2.0 liblzma openssl libjpeg  libturbojpeg libpng libwebp  libwebpdecoder  libwebpdemux  libwebpmux)" \
            LDFLAGS="$(pkg-config  --libs-only-L   --static libzip zlib libzstd freetype2 libxml-2.0 liblzma openssl libjpeg  libturbojpeg libpng libwebp  libwebpdecoder  libwebpdemux  libwebpmux)" \
            LIBS="$(pkg-config     --libs-only-l   --static libzip zlib libzstd freetype2 libxml-2.0 liblzma openssl libjpeg  libturbojpeg libpng libwebp  libwebpdecoder  libwebpdemux  libwebpmux)" \
            ./configure \
            --prefix={$imagemagick_prefix} \
            --enable-static \
            --disable-shared \
            --with-zip=yes \
            --with-fontconfig=no \
            --with-heic=no \
            --with-lcms=no \
            --with-lqr=no \
            --with-openexr=no \
            --with-openjp2=no \
            --with-pango=no \
            --with-jpeg=yes \
            --with-png=yes \
            --with-webp=yes \
            --with-raw=yes \
            --with-tiff=yes \
            --with-zstd=yes \
            --with-lzma=yes \
            --with-xml=yes \
            --with-zip=yes \
            --with-zlib=yes \
            --with-zstd=yes \
            --with-freetype=yes 

EOF
            )
            ->withPkgName('ImageMagick')
            ->withLicense('https://imagemagick.org/script/license.php', Library::LICENSE_APACHE2)
            ->depends('libxml2', 'libzip', 'zlib', 'libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif','openssl','libzstd')

    );
    $p->addExtension((new Extension('imagick'))
        ->withOptions('--with-imagick=' . IMAGEMAGICK_PREFIX)
        ->withPeclVersion('3.6.0')
        ->withHomePage('https://github.com/Imagick/imagick')
        ->withLicense('https://github.com/Imagick/imagick/blob/master/LICENSE', Extension::LICENSE_PHP)
        ->depends('imagemagick')
    );
};
