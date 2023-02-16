<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $lib = new Library('libjpeg');
    $lib->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
        ->withFile('libjpeg-turbo-2.1.2.tar.gz')
        ->withPrefix('/usr/libjpeg')
        ->withConfigure('cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr/libjpeg .')
        ->withLdflags('-L/usr/libjpeg/lib64')
        ->withPkgConfig('/usr/libjpeg/lib64/pkgconfig')
        ->withHomePage('https://libjpeg-turbo.org/')
        ->withLicense('https://github.com/libjpeg-turbo/libjpeg-turbo/blob/main/LICENSE.md', Library::LICENSE_BSD);
    if ($p->getOsType() === 'macos') {
        $lib->withScriptAfterInstall('find ' . $lib->prefix . ' -name \*.dylib | xargs rm -f');
    }
    $p->addLibrary($lib);



    $p->addLibrary(
        (new Library('libpng'))
            ->withPrefix('/usr/libpng')
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withConfigure('./configure --prefix=/usr/libpng --enable-static --disable-shared')
            ->withLicense('http://www.libpng.org/pub/png/src/libpng-LICENSE.txt', Library::LICENSE_SPEC)
    );

    $p->addLibrary(
        (new Library('libgif'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
            ->withPrefix('libgif')
            ->withMakeOptions('libgif.a')
            ->withMakeInstallCommand('')
            ->withScriptBeforeInstall('
            test -d /usr/libgif/ && rm -rf /usr/libgif/
            mkdir -p /usr/libgif/
            ')
            ->withScriptAfterInstall('cp libgif.a /usr/lib && cp gif_lib.h /usr/include')
            ->withLicense('https://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
            ->depends('libjpeg','bzip2','zlib')
    );
    $p->addLibrary(
        (new Library('libwebp'))
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withPrefix('/usr/libwebp')
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr/libwebp --enable-static --disable-shared')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withHomePage('https://github.com/webmproject/libwebp')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
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
