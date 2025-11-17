<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $bzip2_prefix = BZIP2_PREFIX;
    $imagemagick_prefix = IMAGEMAGICK_PREFIX;
    $p->addLibrary(
        (new Library('imagemagick'))
            ->withHomePage('https://imagemagick.org/index.php')
            ->withManual('https://github.com/ImageMagick/ImageMagick.git')
            ->withLicense('https://imagemagick.org/script/license.php', Library::LICENSE_APACHE2)
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.2-8.tar.gz')
            ->withFile('ImageMagick-v7.1.2-8.tar.gz')
            //->withFileHash('md5', '90eb1a9263b47b7e844eb817fe08932c')
            ->withPrefix($imagemagick_prefix)
            ->withBuildCached(false)
            ->withConfigure(
                <<<EOF
            ./configure --help
            PACKAGES_NAMES="libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux  libpng freetype2"
            PACKAGES_NAMES="\${PACKAGES_NAMES} libbrotlicommon libbrotlidec libbrotlienc libzip  zlib  libzstd  liblzma"
            PACKAGES_NAMES="\${PACKAGES_NAMES} libcrypto libssl   openssl"
            PACKAGES_NAMES="\${PACKAGES_NAMES} libxml-2.0"
            PACKAGES_NAMES="\${PACKAGES_NAMES} libheif lcms2 libraw libraw_r libtiff-4 "
            PACKAGES_NAMES="\${PACKAGES_NAMES} libdeflate "
            CPPFLAGS="\$(pkg-config --cflags-only-I --static \$PACKAGES_NAMES ) -I{$bzip2_prefix}/include" \
            LDFLAGS="\$(pkg-config  --libs-only-L   --static \$PACKAGES_NAMES ) -L{$bzip2_prefix}/lib"  \
            LIBS="\$(pkg-config     --libs-only-l   --static \$PACKAGES_NAMES ) -lbz2" \
            ./configure \
            --prefix={$imagemagick_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-pic \
            --with-zip \
            --with-zlib \
            --with-lzma \
            --with-zstd \
            --with-jpeg \
            --with-png \
            --with-webp \
            --with-xml \
            --with-freetype \
            --with-heic \
            --with-raw \
            --with-tiff \
            --with-lcms \
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
            --without-jbig \
            --without-jxl \
            --without-openjp2 \
            --without-lqr \
            --without-openexr \
            --without-pango \
            --without-x \
            --without-modules \
            --with-magick-plus-plus \
            --without-utilities \
            --without-gvc \
            --without-autotrace \
            --without-dps \
            --without-fftw \
            --without-flif \
            --without-fpx \
            --without-gslib \
            --without-perl \
            --without-raqm \
            --without-wmf

EOF
            )
            ->withPkgName('ImageMagick-7.Q16HDRI')
            ->withPkgName('ImageMagick')
            ->withPkgName('MagickCore-7.Q16HDRI')
            ->withPkgName('MagickCore')
            ->withPkgName('MagickWand-7.Q16HDRI')
            ->withPkgName('MagickWand')
            ->withPkgName('Magick++-7.Q16HDRI')
            ->withPkgName('Magick++')
            ->withBinPath($imagemagick_prefix . '/bin/')
            ->withDependentLibraries(
                'libxml2',
                'libzip',
                'zlib',
                'liblzma',
                'libjpeg',
                'freetype',
                'libwebp',
                'libpng',
                'libgif',
                'openssl',
                'libzstd',
                'libheif',
                'lcms2',
                'libraw',
                'libtiff',
                'libdeflate',
            //'libjxl' //咱不启用
            )
    );
};
