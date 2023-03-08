<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

function install_libjpeg(Preprocessor $p)
{
    $libjpeg_prefix = JPEG_PREFIX;
    $lib = new Library('libjpeg');
    $lib->withHomePage('https://libjpeg-turbo.org/')
        ->withLicense('https://github.com/libjpeg-turbo/libjpeg-turbo/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
        ->withFile('libjpeg-turbo-2.1.2.tar.gz')
        ->withPrefix($libjpeg_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libjpeg_prefix)
        ->withConfigure('cmake -G"Unix Makefiles" -DENABLE_STATIC=1 -DENABLE_SHARED=0  -DCMAKE_INSTALL_PREFIX=' . $libjpeg_prefix . ' .')
        ->withPkgName('libjpeg');

    // linux 系统中是保存在 /usr/lib64 目录下的，而 macos 是放在 /usr/lib 目录中的，不清楚这里是什么原因？
    $jpeg_lib_dir = $libjpeg_prefix . '/' . ($p->getOsType() === 'macos' ? 'lib' : 'lib64');
    $lib->withLdflags('-L' . $jpeg_lib_dir)
        ->withPkgConfig($jpeg_lib_dir . '/pkgconfig');
    if ($p->getOsType() === 'macos') {
        $lib->withScriptAfterInstall('find ' . $lib->prefix . ' -name \*.dylib | xargs rm -f');
    }
    $p->addLibrary($lib);
}

function install_libgif(Preprocessor $p)
{
    $libgif_prefix = GIF_PREFIX;
    $p->addLibrary(
        (new Library('libgif'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
            ->withLicense('https://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
            ->withPrefix($libgif_prefix)
            ->withCleanBuildDirectory()
            ->withCleanBuildDirectory()
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
            ->withPkgName('')
            ->withPkgConfig('')
    );
    if (0) {
        $p->addLibrary(
            (new Library('giflib'))
                ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
                ->withLicense('http://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
                ->withCleanBuildDirectory()
                ->withPrefix('/usr/giflib')
                ->withScriptBeforeConfigure(
                    '

                default_prefix_dir="/ u s r" # 阻止 macos 系统下编译路径被替换
                # 替换空格
                default_prefix_dir=$(echo "$default_prefix_dir" | sed -e "s/[ ]//g")

                sed -i.bakup "s@PREFIX = $default_prefix_dir/local@PREFIX = /usr/giflib@" Makefile

                cat >> Makefile <<"EOF"
install-lib-static:
    $(INSTALL) -d "$(DESTDIR)$(LIBDIR)"
    $(INSTALL) -m 644 libgif.a "$(DESTDIR)$(LIBDIR)/libgif.a"
EOF


                '
                )
                ->withMakeOptions('libgif.a')
                //->withMakeOptions('all')
                ->withMakeInstallOptions('install-include && make  install-lib-static')
                # ->withMakeInstallCommand('install-include DESTDIR=/usr/giflib && make  install-lib-static DESTDIR=/usr/giflib')
                # ->withMakeInstallOptions('DESTDIR=/usr/libgif')
                ->withLdflags('-L/usr/giflib/lib')
                ->disableDefaultPkgConfig()
        );
    }
}

function install_libpng(Preprocessor $p)
{
    $libpng_prefix = PNG_PREFIX;
    $libzlib_prefix = ZLIB_PREFIX;
    $p->addLibrary(
        (new Library('libpng'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withLicense('http://www.libpng.org/pub/png/src/libpng-LICENSE.txt', Library::LICENSE_SPEC)
            ->withPrefix($libpng_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libpng_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static zlib )" \
                LDFLAGS="$(pkg-config --libs-only-L      --static zlib )" \
                LIBS="$(pkg-config --libs-only-l         --static zlib )" \
                ./configure \
                --prefix={$libpng_prefix} \
                --enable-static \
                --disable-shared \
                --with-zlib-prefix={$libzlib_prefix} \
                --with-binconfigs
EOF
            )
            ->withPkgName('libpng16')
            ->withBinPath($libpng_prefix . '/bin')
            ->depends('zlib')
    );
}

function install_libwebp(Preprocessor $p)
{
    $libwebp_prefix = WEBP_PREFIX;
    $libpng_prefix = PNG_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $libgif_prefix = GIF_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $jpeg_lib_dir = $libjpeg_prefix . '/' . ($p->getOsType() === 'macos' ? 'lib' : 'lib64');
    $p->addLibrary(
        (new Library('libwebp'))
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withHomePage('https://github.com/webmproject/libwebp')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
            ->withPrefix($libwebp_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libwebp_prefix)
            ->withConfigure(
                <<<EOF
                ./autogen.sh
                ./configure --help
                ./configure --help | grep -e '--disable'
                ./configure --help | grep -e '--enable'
                ./configure --help | grep -e '--with'

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
                --with-jpeglibdir={$jpeg_lib_dir} \
                --with-gifincludedir={$libgif_prefix}/include \
                --with-giflibdir={$libgif_prefix}/lib \
                --disable-tiff

EOF
            )
            ->withPkgName('libwebp')
            ->withLdflags('-L' . $libwebp_prefix . '/lib -lwebpdemux -lwebpmux')
            ->withBinPath($libwebp_prefix . '/bin')
            ->depends('libpng', 'libjpeg', 'libgif')
    );
}



function install_freetype(Preprocessor $p)
{
    $freetype_prefix = FREETYPE_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $libpng_prefix = PNG_PREFIX;
    $libzlib_prefix = ZLIB_PREFIX;
    $p->addLibrary(
        (new Library('freetype'))
            ->withPrefix($freetype_prefix)
            ->withUrl('https://download.savannah.gnu.org/releases/freetype/freetype-2.10.4.tar.gz')
            ->withLicense(
                'https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/FTL.TXT',
                Library::LICENSE_SPEC
            )
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($freetype_prefix)
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
            ->withHomePage('https://freetype.org/')
            ->withPkgName('freetype2')
            ->depends('zlib', 'bzip2', 'libpng', 'brotli')
    );
}



function install_libtiff(Preprocessor $p)
{
    $libtiff_prefix = LIBTIFF_PREFIX;
    $lib = new Library('libtiff');
    $lib->withHomePage('http://www.libtiff.org/')
        ->withLicense('https://gitlab.com/libtiff/libtiff/-/blob/master/LICENSE.md', Library::LICENSE_SPEC)
        ->withUrl('http://download.osgeo.org/libtiff/tiff-4.5.0.tar.gz')
        ->withPrefix($libtiff_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libtiff_prefix)
        ->withConfigure(
            <<<'EOF'
            ./configure --help
            ./configure --help | grep -e '--enable'
            ./configure --help | grep -e '--disable'

            package_names="zlib libjpeg libturbojpeg liblzma  libzstd "

            CPPFLAGS=$(pkg-config  --cflags-only-I --static $package_names ) \
            LDFLAGS=$(pkg-config   --libs-only-L   --static $package_names ) \
            LIBS=$(pkg-config      --libs-only-l   --static $package_names ) \
EOF
            . PHP_EOL .
            <<<EOF
            ./configure --prefix={$libtiff_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --disable-docs \
            --disable-tests \
            --disable-webp

EOF
        )
        ->withBinPath($libtiff_prefix . '/bin')
        ->withPkgName('libtiff');

    $p->addLibrary($lib);
}



function install_libraw(Preprocessor $p)
{
    $libraw_prefix = LIBRAW_PREFIX;
    $lib = new Library('libraw');
    $lib->withHomePage('https://www.libraw.org/about')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://www.libraw.org/data/LibRaw-0.21.1.tar.gz')

        ->withPrefix($libraw_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libraw_prefix)
        ->withConfigure(
            <<<'EOF'
            ./configure --help
            # ZLIB_CFLAGS=$(pkg-config  --cflags --static zlib )
            # ZLIB_LIBS=$(pkg-config    --libs   --static zlib )


            package_names="zlib libjpeg libturbojpeg "
            CPPFLAGS=$(pkg-config  --cflags-only-I --static $package_names ) \
            LDFLAGS=$(pkg-config   --libs-only-L   --static $package_names ) \
            LIBS=$(pkg-config      --libs-only-l   --static $package_names ) \
            LIBS="-lstdc++" \
EOF
            . PHP_EOL .
            <<<EOF
            ./configure \
            --prefix={$libraw_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-jpeg \
            --enable-zlib
EOF
        )
        ->withPkgName('librawc  libraw_r');

    $p->addLibrary($lib);
}



/**
 * 参考文档 https://zhuanlan.zhihu.com/p/355256489
 * AVIF是一种基于AV1视频编码的新图像格式，相对于JPEG，WEBP这类图片格式来说，它的压缩率更高，并且画面细节更好。而最关键的是，它是免费且开源的，没有任何授权费用。
 *
 * HEIC是新出的一种图像格式 与JPG相比，它占用的空间更小，画质更加无损 HEIC使用的图像压缩编解码器最早是为视频开发的。
 * 高效视频编码（HEVC）用离散余弦和正弦变换（DCT和DST）压缩视频的每一帧
 * HEIC的效率是JPEG的两倍  作为iPhone的默认格式
 *
 * OpenEXR 视觉特效行业使用的一种文件格式,适用于高动态范围图像和HDR标准。 这种胶片格式具有适合电影制作的色彩保真度和动态范围
 *
 * openjp2
 * 参考文档：https://blog.csdn.net/Ruky_Z/article/details/100606195
 * openslide是处理医学图像， 医学图像最显著的一个特征就是“大”，如何处理这种“大”，目前常用的一种方法就是切割，将一个大的WSI切割成多个小tile，然后分别对多个tile进行处理，“化大为小”。
 *
 * 参考文档 ：https://zhuanlan.zhihu.com/p/504610500
 * JPEG XL 能在实现接近无损的视觉效果的同时，提供良好的压缩效果  它旨在超越现有的位图格式，并成为它们的通用替代
 *
 * 谷歌将专注于最终进一步推进 WebP 和 AVIF 图像格式
 *
 * @param Preprocessor $p
 * @return void
 */
function install_imagemagick(Preprocessor $p)
{
    $bzip2_prefix = BZIP2_PREFIX;
    $imagemagick_prefix = IMAGEMAGICK_PREFIX;
    $p->addLibrary(
        (new Library('imagemagick'))
            ->withHomePage('https://imagemagick.org/index.php')
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.0-62.tar.gz')
            ->withLicense('https://imagemagick.org/script/license.php', Library::LICENSE_APACHE2)
            ->withManual('https://github.com/ImageMagick/ImageMagick.git')
            ->withPrefix($imagemagick_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($imagemagick_prefix)
            ->withFile('ImageMagick-v7.1.0-62.tar.gz')
            ->withPrefix($imagemagick_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help

            CPPFLAGS="$(pkg-config --cflags-only-I --static libzip zlib libzstd freetype2 libxml-2.0 liblzma openssl libjpeg  libturbojpeg libpng libwebp  libwebpdecoder  libwebpdemux  libwebpmux) -I{$bzip2_prefix}/include" \
            LDFLAGS="$(pkg-config  --libs-only-L   --static libzip zlib libzstd freetype2 libxml-2.0 liblzma openssl libjpeg  libturbojpeg libpng libwebp  libwebpdecoder  libwebpdemux  libwebpmux) -L{$bzip2_prefix}/lib" \
            LIBS="$(pkg-config     --libs-only-l   --static libzip zlib libzstd freetype2 libxml-2.0 liblzma openssl libjpeg  libturbojpeg libpng libwebp  libwebpdecoder  libwebpdemux  libwebpmux) -lbz2" \
            ./configure \
            --prefix={$imagemagick_prefix} \
            --enable-static \
            --disable-shared \
            --with-zip \
            --with-zlib \
            --with-lzma \
            --with-zstd \
            --with-jpeg \
            --with-png \
            --with-webp \
            --with-raw \
            --with-tiff \
            --with-xml \
            --with-freetype=yes \
            --enable-hdri \
            --enable-opencl \
            --without-djvu \
            --without-rsvg \
            --without-fontconfig \
            --without-heic \
            --without-jbig \
            --without-jxl \
            --without-lcms \
            --without-openjp2 \
            --without-lqr \
            --without-openexr \
            --without-pango \
            --without-utilities

EOF
            )
            ->withPkgName('ImageMagick')
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
}
