<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $lib = new Library('libjpeg');
    $lib->withHomePage('https://libjpeg-turbo.org/')
        ->withLicense('https://github.com/libjpeg-turbo/libjpeg-turbo/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
        ->withFile('libjpeg-turbo-2.1.2.tar.gz')
        ->withPrefix('/usr/libjpeg')
        ->withConfigure('cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr/libjpeg .')
        ->withLdflags('-L/usr/libjpeg/lib')
        ->withPkgConfig('/usr/libjpeg/lib/pkgconfig')
        ->withPkgName('libjpeg') ;

    if ($p->getOsType() === 'macos') {
        $lib->withScriptAfterInstall('find ' . $lib->prefix . ' -name \*.dylib | xargs rm -f');
    }
    $p->addLibrary($lib);

    $p->addLibrary(
        (new Library('libpng'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withLicense('http://www.libpng.org/pub/png/src/libpng-LICENSE.txt', Library::LICENSE_SPEC)
            ->withPrefix('/usr/libpng')
            ->withConfigure('
            ./configure --prefix=/usr/libpng --enable-static --disable-shared \
            --with-zlib-prefix=/usr/zlib \
            --with-binconfigs
            ')
            ->withPkgName('libpng16')
            ->depends('zlib')

    );

    $p->addLibrary(
        (new Library('libgif'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
            ->withLicense('https://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
            ->withPrefix('libgif')
            ->withMakeOptions('libgif.a')
            ->withMakeInstallCommand('')
            ->withScriptBeforeInstall('
            test -d /usr/libgif/ && rm -rf /usr/libgif/
            mkdir -p /usr/libgif/lib
            mkdir -p /usr/libgif/include
            ')
            ->withScriptAfterInstall('cp libgif.a /usr/libgif/lib && cp gif_lib.h /usr/libgif/include')
            ->withLdflags('-L/usr/libgif/lib')
            ->withPkgName('')
            ->withPkgConfig('')
    );
    $p->addLibrary(
        (new Library('libwebp'))
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withHomePage('https://github.com/webmproject/libwebp')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
            ->withPrefix('/usr/libwebp')
            ->withConfigure('
            ./autogen.sh && ./configure --prefix=/usr/libwebp --enable-static --disable-shared \
             --enable-libwebpdecoder \
             --enable-libwebpextras \
             --with-pngincludedir=/usr/libpng/include \
             --with-pnglibdir=/usr/libpng/lib \
             --with-jpegincludedir=/usr/libjpeg/include \
             --with-jpeglibdir=/usr/libjpeg/lib64 \
             --with-gifincludedir=/usr/libgif/include \
             --with-giflibdir=/usr/libgif/lib
            ')
            ->withPkgName('libwebp')
            ->depends('libpng','libjpeg','libgif')

    );
    $p->addLibrary(
        (new Library('freetype'))
            ->withPrefix('/usr/freetype')
            ->withUrl('https://download.savannah.gnu.org/releases/freetype/freetype-2.10.4.tar.gz')
            ->withLicense('https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/FTL.TXT', Library::LICENSE_SPEC)
            ->withConfigure(<<<EOF
            export BZIP2_CFLAGS='-I/usr/bzip2/include'
            export BZIP2_LIBS='-L/usr/bzip2/lib -lbz2'
            ./configure --prefix=/usr/freetype \
            --enable-static \
            --disable-shared  \
            --with-zlib=yes \
            --with-bzip2=yes \
            --with-png=yes \
            --with-harfbuzz=no \
            --with-brotli=no
EOF
            )
            ->withHomePage('https://freetype.org/')
            ->withPkgName('freetype2')
            ->depends('zlib','libpng')

    );

    $p->addExtension((new Extension('gd'))
        ->withOptions('--enable-gd --with-jpeg --with-freetype --with-webp')
        ->depends('libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif')
    );
};
