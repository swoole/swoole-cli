<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('imagemagick' ))
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.0-19.tar.gz')
            ->withPrefix('/usr/imagemagick')
            ->withConfigure(<<<EOF
              ./configure \
              --prefix=/usr/imagemagick \
              --enable-static\
              --disable-shared \
              --with-zip=no \
              --with-fontconfig=no \
              --with-heic=no \
              --with-lcms=no \
              --with-lqr=no \
              --with-openexr=no \
              --with-openjp2=no \
              --with-pango=no \
              --with-raw=no \
              --with-tiff=no \
              --with-zstd=no \
              --with-freetype=yes
EOF
            )
            ->withPkgName('ImageMagick')
            ->withLicense('https://imagemagick.org/script/license.php', Library::LICENSE_APACHE2)
            ->depends('libxml2', 'zip', 'zlib', 'libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif')
    );
    $p->addExtension((new Extension('imagick'))
        ->withOptions('--with-imagick=/usr/imagemagick')
        ->withPeclVersion('3.6.0')
        ->withHomePage('https://github.com/Imagick/imagick')
        ->withLicense('https://github.com/Imagick/imagick/blob/master/LICENSE', Extension::LICENSE_PHP)
        ->depends('imagemagick')
    );
};
