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
        ->withPkgName('libjpeg')
        ->withBinPath($libjpeg_prefix . '/bin/')
    ;

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
            ->withCleanPreInstallDirectory($libgif_prefix)
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
            ->withPkgName('libpng')
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
            ->withHomePage('https://freetype.org/')
            ->withUrl('https://download.savannah.gnu.org/releases/freetype/freetype-2.10.4.tar.gz')
            ->withLicense(
                'https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/GPLv2.TXT',
                Library::LICENSE_GPL
            )
            ->withPrefix($freetype_prefix)
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



function install_lcms2(Preprocessor $p)
{
    $lcms2_prefix = LCMS2_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $libtiff_prefix = LIBTIFF_PREFIX;
    $lib = new Library('lcms2');
    $lib->withHomePage('https://littlecms.com/color-engine/')
        ->withLicense('https://www.opensource.org/licenses/mit-license.php', Library::LICENSE_MIT)
        ->withUrl('https://jaist.dl.sourceforge.net/project/lcms/lcms/2.15/lcms2-2.15.tar.gz')
        ->withManual('https://lfs.lug.org.cn/blfs/view/10.0/general/lcms2.html')
        ->withPrefix($lcms2_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($lcms2_prefix)
        ->withConfigure(
            <<<EOF
            ./configure --help

            package_names="zlib"
            CPPFLAGS="\$(pkg-config  --cflags-only-I --static \$package_names )" \
            LDFLAGS="\$(pkg-config   --libs-only-L   --static \$package_names )" \
            LIBS="\$(pkg-config      --libs-only-l   --static \$package_names )" \
            ./configure \
            --prefix={$lcms2_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --with-jpeg={$libjpeg_prefix} \
            --with-tiff={$libtiff_prefix}

EOF
        )
        ->withBinPath($lcms2_prefix . '/bin/')
        ->withPkgName('lcms2');

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
 *  颜色管理引擎 https://littlecms.com/color-engine/
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
            ->withFile('ImageMagick-v7.1.0-62.tar.gz')
            ->withMd5sum('37b896e9eecd379a6cd0d6359b9f525a')
            ->withPrefix($imagemagick_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($imagemagick_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            # lcms2 libtiff-4 libraw libraw_r
            # export RAW_R_CFLAGS=$(pkg-config  --cflags-only-I --static libraw_r )
            # export RAW_R_LIBS=$(pkg-config    --libs-only-l   --static libraw_r )

            # export TIFF_CFLAGS=$(pkg-config  --cflags-only-I --static libtiff-4 )
            # export TIFF_LIBS=$(pkg-config    --libs-only-l   --static libtiff-4 )

            #  HEIF_CFLAGS C compiler flags for HEIF, overriding pkg-config
            #  HEIF_LIBS   linker flags for HEIF, overriding pkg-config
            #  JXL_CFLAGS  C compiler flags for JXL, overriding pkg-config
            #  JXL_LIBS    linker flags for JXL, overriding pkg-config

            # export LCMS2_CFLAGS=$(pkg-config  --cflags-only-I --static lcms2 )
            # export LCMS2_LIBS=$(pkg-config    --libs-only-l   --static lcms2 )
            
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
            --with-modules \
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
}
