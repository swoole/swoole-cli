<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

function install_libjpeg(Preprocessor $p)
{

}

function install_libgif(Preprocessor $p)
{

}

function install_libpng(Preprocessor $p)
{
}

function install_libwebp(Preprocessor $p)
{

}


function install_freetype(Preprocessor $p)
{

}


function install_libtiff(Preprocessor $p)
{

}


function install_lcms2(Preprocessor $p): void
{

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
 *
 * @param Preprocessor $p
 * @return void
 */
function install_imagemagick(Preprocessor $p): void
{
    /**
     * # lcms2 libtiff-4 libraw libraw_r
     * # export RAW_R_CFLAGS=$(pkg-config  --cflags-only-I --static libraw_r )
     * # export RAW_R_LIBS=$(pkg-config    --libs-only-l   --static libraw_r )
     *
     * # export TIFF_CFLAGS=$(pkg-config  --cflags-only-I --static libtiff-4 )
     * # export TIFF_LIBS=$(pkg-config    --libs-only-l   --static libtiff-4 )
     *
     * #  HEIF_CFLAGS C compiler flags for HEIF, overriding pkg-config
     * #  HEIF_LIBS   linker flags for HEIF, overriding pkg-config
     * #  JXL_CFLAGS  C compiler flags for JXL, overriding pkg-config
     * #  JXL_LIBS    linker flags for JXL, overriding pkg-config
     *
     * # export LCMS2_CFLAGS=$(pkg-config  --cflags-only-I --static lcms2 )
     * # export LCMS2_LIBS=$(pkg-config    --libs-only-l   --static lcms2 )
     */

    $bzip2_prefix = BZIP2_PREFIX;
    $imagemagick_prefix = IMAGEMAGICK_PREFIX;
    $p->addLibrary(
        (new Library('imagemagick'))
            ->withHomePage('https://imagemagick.org/index.php')
            ->withManual('https://github.com/ImageMagick/ImageMagick.git')
            ->withLicense('https://imagemagick.org/script/license.php', Library::LICENSE_APACHE2)
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.0-62.tar.gz')
            ->withFile('ImageMagick-v7.1.0-62.tar.gz')
            ->withMd5sum('37b896e9eecd379a6cd0d6359b9f525a')
            ->withPrefix($imagemagick_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            PACKAGES_NAMES="libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux  libpng freetype2"
            PACKAGES_NAMES="\${PACKAGES_NAMES} libbrotlicommon libbrotlidec libbrotlienc libzip  zlib  libzstd  liblzma"
            PACKAGES_NAMES="\${PACKAGES_NAMES} libcrypto libssl   openssl"
            PACKAGES_NAMES="\${PACKAGES_NAMES} libxml-2.0"
            CPPFLAGS="\$(pkg-config --cflags-only-I --static \$PACKAGES_NAMES ) -I{$bzip2_prefix}/include" \
            LDFLAGS="\$(pkg-config  --libs-only-L   --static \$PACKAGES_NAMES ) -L{$bzip2_prefix}/lib"  \
            LIBS="\$(pkg-config     --libs-only-l   --static \$PACKAGES_NAMES ) -lbz2" \
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
            --without-x \
            --without-modules \
            --without-magick-plus-plus \
            --without-utilities \
            --without-gvc \
            --without-autotrace \
            --without-dps \
            --without-fftw \
            --without-flif \
            --without-fpx \
            --without-gslib \
            --without-ltdl \
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
            ->withBinPath($imagemagick_prefix . '/bin/')
            ->depends(
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
                'libzstd'
            )
    );
}
