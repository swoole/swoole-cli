<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $libjpeg_prefix = JPEG_PREFIX;
    // linux 系统中是保存在 /usr/lib64 目录下的，而 macos 是放在 /usr/lib 目录中的，不清楚这里是什么原因？
    $jpeg_lib_dir = $libjpeg_prefix . '/' . ($p->getOsType() === 'macos' ? 'lib' : 'lib64');
    $lib = new Library('libjpeg');
    $lib->withHomePage('https://libjpeg-turbo.org/')
        ->withManual('https://libjpeg-turbo.org/Documentation/Documentation')
        ->withLicense('https://github.com/libjpeg-turbo/libjpeg-turbo/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
        ->withFile('libjpeg-turbo-2.1.2.tar.gz')
        ->withPrefix($libjpeg_prefix)
        ->withConfigure(
            <<<EOF
            cmake -G"Unix Makefiles"   . \
            -DCMAKE_INSTALL_PREFIX={$libjpeg_prefix} \
            -DCMAKE_INSTALL_LIBDIR={$libjpeg_prefix}/lib \
            -DCMAKE_INSTALL_INCLUDEDIR={$libjpeg_prefix}/include \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_STATIC_LIBS=ON 
EOF
        )
        ->withScriptAfterInstall(
            <<<EOF
            rm -rf {$libjpeg_prefix}/lib/*.so.*
            rm -rf {$libjpeg_prefix}/lib/*.so
            rm -rf {$libjpeg_prefix}/lib/*.dylib
EOF
        )
        ->withPkgName('libjpeg')
        ->withPkgName('libturbojpeg')
        ->withBinPath($libjpeg_prefix . '/bin/');
    $p->addLibrary($lib);

    $libpng_prefix = PNG_PREFIX;
    $libzlib_prefix = ZLIB_PREFIX;
    $p->addLibrary(
        (new Library('libpng'))
            ->withHomePage('http://www.libpng.org/pub/png/libpng.html')
            ->withLicense('http://www.libpng.org/pub/png/src/libpng-LICENSE.txt', Library::LICENSE_SPEC)
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withPrefix($libpng_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static zlib )" \
                LDFLAGS="$(pkg-config   --libs-only-L    --static zlib )" \
                LIBS="$(pkg-config      --libs-only-l    --static zlib )" \
                ./configure --prefix={$libpng_prefix} \
                --enable-static --disable-shared \
                --with-zlib-prefix={$libzlib_prefix} \
                --with-binconfigs 
EOF
            )
            ->withPkgName('libpng')
            ->withPkgName('libpng16')
            ->withBinPath($libpng_prefix . '/bin')
            ->depends('zlib')
    );

    $libgif_prefix = GIF_PREFIX;
    $p->addLibrary(
        (new Library('libgif'))
            ->withHomePage('https://giflib.sourceforge.net/')
            ->withManual('https://giflib.sourceforge.net/intro.html')
            ->withLicense('https://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
            ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
            ->withPrefix($libgif_prefix)
            ->withMakeOptions('libgif.a')
            ->withMakeInstallCommand('')
            ->withScriptAfterInstall(
                <<<EOF
                if [ ! -d {$libgif_prefix}/lib ]; then
                    mkdir -p {$libgif_prefix}/lib
                fi
                if [ ! -d {$libgif_prefix}/include ]; then
                    mkdir -p {$libgif_prefix}/include
                fi
                cp libgif.a {$libgif_prefix}/lib/libgif.a
                cp gif_lib.h {$libgif_prefix}/include/gif_lib.h
                EOF
            )
            ->withLdflags('-L' . $libgif_prefix . '/lib')
    );

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libgif_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libgif_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lgif');

    $libwebp_prefix = WEBP_PREFIX;
    $p->addLibrary(
        (new Library('libwebp'))
            ->withHomePage('https://chromium.googlesource.com/webm/libwebp')
            ->withManual('https://chromium.googlesource.com/webm/libwebp/+/HEAD/doc/building.md')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withPrefix($libwebp_prefix)
            ->withConfigure(
                <<<EOF
                ./autogen.sh
                ./configure --help
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static libpng libjpeg )" \
                LDFLAGS="$(pkg-config --libs-only-L      --static libpng libjpeg )" \
                LIBS="$(pkg-config --libs-only-l         --static libpng libjpeg )" \
                ./configure --prefix={$libwebp_prefix} \
                --enable-static --disable-shared \
                --enable-libwebpdecoder \
                --enable-libwebpextras \
                --with-pngincludedir={$libpng_prefix}/include \
                --with-pnglibdir={$libpng_prefix}/lib \
                --with-jpegincludedir={$libjpeg_prefix}/include \
                --with-jpeglibdir={$libjpeg_prefix}/lib \
                --with-gifincludedir={$libgif_prefix}/include \
                --with-giflibdir={$libgif_prefix}/lib \
                --disable-tiff  
EOF
            )
            ->withPkgName('libwebp')
            ->withLdflags('-L' . $libwebp_prefix . '/lib -lwebpdemux -lwebpmux')
            ->withBinPath($libwebp_prefix . '/bin/')
            ->depends('libpng', 'libjpeg', 'libgif')
    );

    $freetype_prefix = FREETYPE_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('freetype'))
            ->withHomePage('https://freetype.org/')
            ->withManual('https://freetype.org/freetype2/docs/documentation.html')
            ->withLicense(
                'https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/GPLv2.TXT',
                Library::LICENSE_GPL
            )
            ->withUrl('https://download.savannah.gnu.org/releases/freetype/freetype-2.10.4.tar.gz')
            ->withPrefix($freetype_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            BZIP2_CFLAGS="-I{$bzip2_prefix}/include"  \
            BZIP2_LIBS="-L{$bzip2_prefix}/lib -lbz2"  \
            CPPFLAGS="$(pkg-config --cflags-only-I --static zlib libpng  libbrotlicommon  libbrotlidec  libbrotlienc)" \
            LDFLAGS="$(pkg-config  --libs-only-L   --static zlib libpng  libbrotlicommon  libbrotlidec  libbrotlienc)" \
            LIBS="$(pkg-config     --libs-only-l   --static zlib libpng  libbrotlicommon  libbrotlidec  libbrotlienc)" \
            ./configure --prefix={$freetype_prefix} \
            --enable-static \
            --disable-shared \
            --with-zlib=yes \
            --with-bzip2=yes \
            --with-png=yes \
            --with-harfbuzz=no  \
            --with-brotli=yes  
EOF
            )
            ->withPkgName('freetype2')
            ->depends('zlib', 'bzip2', 'libpng', 'brotli')
    );

    $p->addExtension(
        (new Extension('gd'))
            ->withHomePage('https://www.php.net/manual/zh/book.image.php')
            ->withOptions('--enable-gd --with-jpeg --with-freetype --with-webp')
            ->depends('libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif')
    );
};
