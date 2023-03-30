<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $bzip2_prefix = BZIP2_PREFIX;
    $imagemagick_prefix = IMAGEMAGICK_PREFIX;
    $p->addLibrary(
        (new Library('imagemagick'))
            ->withHomePage('https://imagemagick.org/index.php')
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.0-62.tar.gz')
            ->withLicense('https://imagemagick.org/script/license.php', Library::LICENSE_APACHE2)
            ->withManual('https://github.com/ImageMagick/ImageMagick.git')
            ->withFile('ImageMagick-v7.1.0-62.tar.gz')
            ->withMd5sum('37b896e9eecd379a6cd0d6359b9f525a')
            ->withPrefix($imagemagick_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help

            package_names="libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux  "
            package_names="\${package_names} libbrotlicommon libbrotlidec    libbrotlienc libcrypto libssl   openssl"

            ZIP_CFLAGS=$(pkg-config  --cflags --static libzip ) \
            ZIP_LIBS=$(pkg-config    --libs   --static libzip ) \
            ZLIB_CFLAGS=$(pkg-config  --cflags --static zlib ) \
            ZLIB_LIBS=$(pkg-config    --libs   --static zlib ) \
            LIBZSTD_CFLAGS=$(pkg-config  --cflags --static libzstd ) \
            LIBZSTD_LIBS=$(pkg-config    --libs   --static libzstd ) \
            FREETYPE_CFLAGS=$(pkg-config  --cflags --static freetype2 ) \
            FREETYPE_LIBS=$(pkg-config    --libs   --static freetype2 ) \
            LZMA_CFLAGS=$(pkg-config  --cflags --static liblzma ) \
            LZMA_LIBS=$(pkg-config    --libs   --static liblzma ) \
            PNG_CFLAGS=$(pkg-config  --cflags --static libpng ) \
            PNG_LIBS=$(pkg-config    --libs   --static libpng ) \
            WEBP_CFLAGS=$(pkg-config  --cflags --static libwebp ) \
            WEBP_LIBS=$(pkg-config    --libs   --static libwebp )  \
            WEBPMUX_CFLAGS=$(pkg-config --cflags --static libwebpmux ) \
            WEBPMUX_LIBS=$(pkg-config   --libs   --static libwebpmux ) \
            XML_CFLAGS=$(pkg-config  --cflags --static libxml-2.0 ) \
            XML_LIBS=$(pkg-config    --libs   --static libxml-2.0 ) \
            CPPFLAGS="\$(pkg-config --cflags-only-I --static \$package_names ) -I{$bzip2_prefix}/include" \
            LDFLAGS="\$(pkg-config  --libs-only-L   --static \$package_names ) -L{$bzip2_prefix}/lib"  \
            LIBS="\$(pkg-config     --libs-only-l   --static \$package_names ) -lbz2" \
            ./configure \
            --prefix={$imagemagick_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-zip \
            --with-zlib \
            --with-lzma \
            --with-zstd \
            --with-jpeg \
            --with-png \
            --with-webp \
            --with-xml \
            --with-freetype \
            --without-raw \
            --without-tiff \
            --without-lcms \
            --enable-zero-configuration \
            --enable-bounds-checking \
            --enable-hdri \
            --disable-dependency-tracking \
            --without-perl \
            --disable-docs \
            --disable-opencl \
            --disable-openmp \
            --without-djvu \
            --without-rsvg \
            --without-fontconfig \
            --without-heic \
            --without-jbig \
            --without-jxl \
            --without-openjp2 \
            --without-lqr \
            --without-openexr \
            --without-pango \
            --without-jbig \
            --without-x \
            --without-modules \
            --without-magick-plus-plus \
            --without-utilities
EOF
            )
            ->withPkgName('ImageMagick-7.Q16HDRI')
            ->withPkgName('ImageMagick')
            ->withPkgName('MagickCore-7.Q16HDRI')
            ->withPkgName('MagickCore')
            ->withPkgName('MagickWand-7.Q16HDRI')
            ->withPkgName('MagickWand')
            ->withBinPath($imagemagick_prefix . '/bin/')
            ->depends(
                'libxml2',
                'libzip',
                'zlib',
                'libjpeg',
                'freetype',
                'libwebp',
                'libpng',
                'libgif',
                'openssl',
                'libzstd'
            )
    );

    $p->addExtension(
        (new Extension('imagick'))
            ->withOptions('--with-imagick=' . IMAGEMAGICK_PREFIX)
            ->withPeclVersion('3.6.0')
            ->withHomePage('https://github.com/Imagick/imagick')
            ->withLicense('https://github.com/Imagick/imagick/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withMd5sum('f7b5e9b23fb844e5eb035203d316bc63')
            ->depends('imagemagick')
    );
};
