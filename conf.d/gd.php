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
        ->withPrefix(JPEG_PREFIX)
        ->withConfigure('cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=' . JPEG_PREFIX . ' .')
        ->withPkgName('libjpeg');

    // linux 系统中是保存在 lib64 目录下的，而 macos 是放在 /usr/libjpeg/lib 目录中的，不清楚这里是什么原因？
    $jpeg_lib_dir = JPEG_PREFIX . '/' . ($p->getOsType() === 'macos' ? 'lib' : 'lib64');
    $lib->withLdflags('-L' . $jpeg_lib_dir)
        ->withPkgConfig($jpeg_lib_dir . '/pkgconfig');
    if ($p->getOsType() === 'macos') {
        $lib->withScriptAfterInstall('find ' . $lib->prefix . ' -name \*.dylib | xargs rm -f');
    }
    $p->addLibrary($lib);

    $p->addLibrary(
        (new Library('libpng'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withLicense('http://www.libpng.org/pub/png/src/libpng-LICENSE.txt', Library::LICENSE_SPEC)
            ->withPrefix(PNG_PREFIX)
            ->withConfigure(
                './configure --prefix=' . PNG_PREFIX . '--enable-static --disable-shared ' .
                '--with-zlib-prefix=/usr/zlib  --with-binconfigs'
            )
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
            test -d ' . GIF_PREFIX . ' && rm -rf /usr/libgif/
            mkdir -p ' . GIF_PREFIX . '/lib
            mkdir -p ' . GIF_PREFIX . '/include
            ')
            ->withScriptAfterInstall('cp libgif.a ' . GIF_PREFIX . '/lib && cp gif_lib.h ' . GIF_PREFIX . '/include')
            ->withLdflags('-L' . GIF_PREFIX . '/lib')
            ->withPkgName('')
            ->withPkgConfig('')
    );
    $p->addLibrary(
        (new Library('libwebp'))
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withHomePage('https://github.com/webmproject/libwebp')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
            ->withPrefix(WEBP_PREFIX)
            ->withConfigure('./autogen.sh && ./configure --prefix={$webp_prefix} --enable-static --disable-shared' .
                '--enable-libwebpdecoder ' .
                '--enable-libwebpextras ' .
                '--with-pngincludedir=' . PNG_PREFIX . '/include ' .
                '--with-pnglibdir=' . PNG_PREFIX . '/lib ' .
                '--with-jpegincludedir=' . JPEG_PREFIX . '/include ' .
                '--with-jpeglibdir=' . JPEG_PREFIX . ' ' .
                '--with-gifincludedir=' . GIF_PREFIX . '/include ' .
                '--with-giflibdir=' . GIF_PREFIX . '/lib'
            )
            ->withPkgName('libwebp')
            ->depends('libpng', 'libjpeg', 'libgif')
    );
    $p->addLibrary(
        (new Library('freetype'))
            ->withPrefix(FREETYPE_PREFIX)
            ->withUrl('https://download.savannah.gnu.org/releases/freetype/freetype-2.10.4.tar.gz')
            ->withLicense('https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/FTL.TXT', Library::LICENSE_SPEC)
            ->withConfigure(
                'export BZIP2_CFLAGS=-I/usr/bzip2/include \ ' . PHP_EOL .
                'export BZIP2_LIBS=-L/usr/bzip2/lib -lbz2 \ ' . PHP_EOL .
                './configure --prefix=' . FREETYPE_PREFIX . ' \ ' . PHP_EOL .
                '--enable-static \ ' . PHP_EOL .
                '--disable-shared \ ' . PHP_EOL .
                '--with-zlib=yes \ ' . PHP_EOL .
                '--with-bzip2=yes \ ' . PHP_EOL .
                '--with-png=yes \ ' . PHP_EOL .
                '--with-harfbuzz=no \ ' . PHP_EOL .
                '--with-brotli=no \ ' . PHP_EOL
            )
            ->withHomePage('https://freetype.org/')
            ->withPkgName('freetype2')
            ->depends('zlib', 'libpng')
    );

    $p->addExtension((new Extension('gd'))
        ->withOptions('--enable-gd --with-jpeg --with-freetype --with-webp')
        ->depends('libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif')
    );
};
