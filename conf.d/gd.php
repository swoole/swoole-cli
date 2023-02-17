<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $lib = new Library('libjpeg');
    // 由于imagick库存在问题，需要将 jpeg/freetype/webp/png/gif 全部放在一个目录下
    $freetype_prefix = $png_prefix = $webp_prefix = $gif_prefix = $jpeg_prefix = '/usr/gd';
    $lib->withHomePage('https://libjpeg-turbo.org/')
        ->withLicense('https://github.com/libjpeg-turbo/libjpeg-turbo/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
        ->withFile('libjpeg-turbo-2.1.2.tar.gz')
        ->withPrefix($jpeg_prefix)
        ->withConfigure('cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=' . $jpeg_prefix . ' .')
        ->withPkgName('libjpeg');

    // linux 系统中是保存在 lib64 目录下的，而 macos 是放在 /usr/libjpeg/lib 目录中的，不清楚这里是什么原因？
    $jpeg_lib_dir = $jpeg_prefix . '/' . ($p->getOsType() === 'macos' ? 'lib' : 'lib64');
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
            ->withPrefix($png_prefix)
            ->withConfigure(<<<EOF
            ./configure --prefix={$png_prefix} --enable-static --disable-shared \
            --with-zlib-prefix=/usr/zlib \
            --with-binconfigs
            EOF
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
            test -d ' . $gif_prefix . ' && rm -rf /usr/libgif/
            mkdir -p ' . $gif_prefix . '/lib
            mkdir -p ' . $gif_prefix . '/include
            ')
            ->withScriptAfterInstall('cp libgif.a ' . $gif_prefix . '/lib && cp gif_lib.h ' . $gif_prefix . '/include')
            ->withLdflags('-L' . $gif_prefix . '/lib')
            ->withPkgName('')
            ->withPkgConfig('')
    );
    $p->addLibrary(
        (new Library('libwebp'))
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withHomePage('https://github.com/webmproject/libwebp')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
            ->withPrefix($webp_prefix)
            ->withConfigure(<<<EOF
            ./autogen.sh && ./configure --prefix={$webp_prefix} --enable-static --disable-shared \
             --enable-libwebpdecoder \
             --enable-libwebpextras \
             --with-pngincludedir={$png_prefix}/include \
             --with-pnglibdir={$png_prefix}/lib \
             --with-jpegincludedir={$jpeg_prefix}/include \
             --with-jpeglibdir={$jpeg_prefix} \
             --with-gifincludedir={$png_prefix}/include \
             --with-giflibdir={$png_prefix}/lib
            EOF
            )
            ->withPkgName('libwebp')
            ->depends('libpng', 'libjpeg', 'libgif')

    );
    $p->addLibrary(
        (new Library('freetype'))
            ->withPrefix($freetype_prefix)
            ->withUrl('https://download.savannah.gnu.org/releases/freetype/freetype-2.10.4.tar.gz')
            ->withLicense('https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/FTL.TXT', Library::LICENSE_SPEC)
            ->withConfigure(<<<EOF
            export BZIP2_CFLAGS='-I/usr/bzip2/include'
            export BZIP2_LIBS='-L/usr/bzip2/lib -lbz2'
            ./configure --prefix={$freetype_prefix} \
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
            ->depends('zlib', 'libpng')
    );

    $p->addExtension((new Extension('gd'))
        ->withOptions('--enable-gd --with-jpeg --with-freetype --with-webp')
        ->depends('libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif')
    );
};
