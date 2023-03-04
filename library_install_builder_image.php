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
                ./configure --prefix={$libpng_prefix} \
                --enable-static --disable-shared \
                --with-zlib-prefix={$libzlib_prefix} \
                --with-binconfigs 
EOF
            )
            ->withPkgName('libpng16')
            ->depends('zlib')
    );
}

function install_libwebp(Preprocessor $p)
{
    $libwebp_prefix = WEBP_PREFIX;
    $libpng_prefix = PNG_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $libgif_prefix = GIF_PREFIX;
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
                ./autogen.sh && \
                ./configure --help &&  \
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
                --with-giflibdir={$libgif_prefix}/lib
EOF
            )
            ->withPkgName('libwebp')
            ->withLdflags('-L' . WEBP_PREFIX . '/lib -lwebpdemux -lwebpmux')
            ->depends('libpng', 'libjpeg', 'libgif')
    );
}


function install_libavif(Preprocessor $p)
{
    $libavif_prefix = LIBAVIF_PREFIX;
    $p->addLibrary(
        (new Library('libavif'))
            ->withUrl('https://github.com/AOMediaCodec/libavif/archive/refs/tags/v0.11.1.tar.gz')
            ->withFile('libavif-v0.11.1.tar.g')
            ->withHomePage('https://aomediacodec.github.io/av1-avif/')
            ->withLicense('https://github.com/AOMediaCodec/libavif/blob/main/LICENSE', Library::LICENSE_SPEC)
            ->withManual('https://github.com/AOMediaCodec/libavif')
            ->withPrefix($libavif_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libavif_prefix)
            ->withConfigure(
                <<<EOF
    
            cmake .  \
            -DMAKE_INSTALL_PREFIX={$libavif_prefix} \
            -DAVIF_BUILD_EXAMPLES=ON \
            -DBUILD_SHARED_LIBS=OFF \
            -DAVIF_CODEC_AOM=OFF \
            -DAVIF_CODEC_DAV1D=OFF \
            -DAVIF_CODEC_LIBGAV1=OFF \
            -DAVIF_CODEC_RAV1E=OFF
            exit 0 
               
EOF
            )
            ->withPkgName('libavif')
            ->withLdflags('')
    );
}


function install_libde265(Preprocessor $p)
{
    $libde265_prefix = LIBDE265_PREFIX;
    $lib = new Library('libde265');
    $lib->withHomePage('https://github.com/strukturag/libde265.git')
        ->withLicense('https://github.com/strukturag/libheif/blob/master/COPYING', Library::LICENSE_GPL)
        ->withUrl('https://github.com/strukturag/libde265/archive/refs/tags/v1.0.11.tar.gz')
        ->withFile('libde265-v1.0.11.tar.gz')

        ->withPrefix($libde265_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libde265_prefix)
        ->withConfigure(
            <<<EOF
        ./autogen.sh
        ./configure --help
        ./configure --prefix={$libde265_prefix} \
        --enable-shared=no \
        --enable-static=yes 
EOF
        )
        ->withPkgName('libde265');

    $p->addLibrary($lib);
}

function install_libheif(Preprocessor $p)
{
    $libheif_prefix = LIBHEIF_PREFIX;
    $lib = new Library('libheif');
    $lib->withHomePage('https://github.com/strukturag/libheif.git')
        ->withLicense('https://github.com/strukturag/libheif/blob/master/COPYING', Library::LICENSE_GPL)
        ->withUrl('https://github.com/strukturag/libheif/releases/download/v1.15.1/libheif-1.15.1.tar.gz')

        ->withPrefix($libheif_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libheif_prefix)
        ->withConfigure(
            <<<'EOF'
            ./configure --help
            
            libde265_CFLAGS=$(pkg-config  --cflags --static libde265 ) \
            libde265_LIBS=$(pkg-config    --libs   --static libde265 ) \
            libpng_CFLAGS=$(pkg-config  --cflags --static libpng ) \
            libpng_LIBS=$(pkg-config    --libs   --static libpng ) \
EOF
            . PHP_EOL .
            <<<EOF
            ./configure \
            --prefix={$libheif_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
        )
        ->withPkgName('libheif');

    $p->addLibrary($lib);
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
            CPPFLAGS="$(pkg-config --cflags-only-I --static zlib libpng  libbrotli  libbrotlidec  libbrotlienc)" \
            LDFLAGS="$(pkg-config  --libs-only-L   --static zlib libpng  libbrotli  libbrotlidec  libbrotlienc)" \
            LIBS="$(pkg-config     --libs-only-l   --static zlib libpng  libbrotli  libbrotlidec  libbrotlienc)" \
            ./configure --prefix={$freetype_prefix} \
            --enable-static \
            --disable-shared \
            --with-zlib=yes \
            --with-bzip2=yes \
            --with-png=yes \
            --with-harfbuzz=no \
            --with-brotli=yes 
EOF
            )
            ->withHomePage('https://freetype.org/')
            ->withPkgName('freetype2')
            ->withBinPath($freetype_prefix . '/bin/')
            ->depends('zlib', 'bzip2', 'libpng', 'brotli')
    );
}


//-lgd -lpng -lz -ljpeg -lfreetype -lm

function install_libgd2($p)
{
    $libgd_prefix = LIBGD_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $lib = new Library('libgd2');
    $lib->withHomePage('https://www.libgd.org/')
        ->withLicense('https://github.com/libgd/libgd/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withUrl('https://github.com/libgd/libgd/releases/download/gd-2.3.3/libgd-2.3.3.tar.gz')
        ->withManual('https://github.com/libgd/libgd.git')
        ->withManual('https://libgd.github.io/pages/docs.html')
        ->withPrefix($libgd_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libgd_prefix)
        ->withConfigure(
            <<<'EOF'
        # 下载依赖
         ./configure --help
         # -lbrotlicommon-static -lbrotlidec-static -lbrotlienc-static
        export CPPFLAGS="$(pkg-config  --cflags-only-I  --static zlib libpng freetype2 libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux  libbrotlicommon  libbrotlidec  libbrotlienc ) " \
        export LDFLAGS="$(pkg-config   --libs-only-L    --static zlib libpng freetype2 libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux  libbrotlicommon  libbrotlidec  libbrotlienc ) " \
        export LIBS="$(pkg-config      --libs-only-l    --static zlib libpng freetype2 libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux  libbrotlicommon  libbrotlidec  libbrotlienc ) " \
        
        echo $LIBS
    
EOF . PHP_EOL . <<<EOF
        ./configure \
        --prefix={$libgd_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --without-freetype \
        --with-libiconv-prefix={$libiconv_prefix}
         # --with-freetype=/usr/freetype \

:<<'_EOF_'       
        mkdir -p build
        cd build
        cmake   ..  \
        -DCMAKE_INSTALL_PREFIX={$libgd_prefix} \
        -DCMAKE_BUILD_TYPE=Release \
        -DENABLE_GD_FORMATS=1 \
        -DENABLE_JPEG=1 \
        -DENABLE_TIFF=1 \
        -DENABLE_ICONV=1 \
        -DENABLE_FREETYPE=1 \
        -DENABLE_FONTCONFIG=1 \
        -DENABLE_WEBP=1 \
        -DENABLE_HEIF=1 \
        -DENABLE_AVIF=1 \
        -DENABLE_WEBP=1 
        
        cmake --build . -- -j$(nproc)
        exit 0 
        cmake --install . 
_EOF_
        
EOF
        )
        ->withMakeInstallCommand('')
        ->withPkgName('libgd2');

    $p->addLibrary($lib);
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
            package_names="zlib libjpeg libturbojpeg liblzma  libzstd libwebp  libwebpdecoder  libwebpdemux  libwebpmux"
            
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
            --disable-tests

EOF
        )
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

function install_libOpenEXR(Preprocessor $p)
{
    $libOpenEXR_prefix = '/usr/libOpenEXR';
    $buildDir = $p->getBuildDir() . '/libOpenEXR/' ;
    $lib = new Library('libOpenEXR');
    $lib->withHomePage('http://www.openexr.com/')
        ->withLicense('https://github.com/AcademySoftwareFoundation/openexr/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://github.com/AcademySoftwareFoundation/openexr/archive/refs/tags/v3.1.5.tar.gz')
        ->withManual('https://github.com/AcademySoftwareFoundation/openexr.git')
        ->withManual('https://openexr.com/en/latest/install.html#install')
        ->withFile('openexr-v3.1.5.tar.gz')
        ->withPrefix($libOpenEXR_prefix)
        //->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libOpenEXR_prefix)
        ->withBuildScript(
            <<<EOF
        
        mkdir -p build_dir
        cd build_dir 
        # cmake ..  -DCMAKE_INSTALL_PREFIX={$libOpenEXR_prefix}
        pwd
        cmake $buildDir   --install-prefix={$libOpenEXR_prefix}
        pwd
        ls -lh .
        cmake --build .  --target install --config Release
EOF
        )
        ->withPkgName('libOpenEXR');

    $p->addLibrary($lib);
}function install_libjxl(Preprocessor $p)
{
    $libjxl_prefix = LIBJXL_PREFIX;
    $lib = new Library('libjxl');
    $lib->withHomePage('https://github.com/libjxl/libjxl.git')
        ->withLicense('https://github.com/libjxl/libjxl/blob/main/LICENSE', Library::LICENSE_BSD)
        ->withUrl('https://github.com/libjxl/libjxl/archive/refs/tags/v0.8.1.tar.gz')
        ->withManual('https://github.com/libjxl/libjxl/blob/main/BUILDING.md')
        ->withFile('libjpegxl-v0.8.1.tar.gz')
        ->withPrefix($libjxl_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libjxl_prefix)
        ->withBuildScript(
            <<<EOF
        ## 会自动 下载依赖 ，如网速不佳，请在环境变量里设置代理地址，用于加速下载
        sh deps.sh
        mkdir build
        cd build
        cmake -DJPEGXL_STATIC=true -DCMAKE_BUILD_TYPE=Release -DBUILD_TESTING=OFF .. 
        cmake --build . -- -j$(nproc)
        exit 0 
        cmake --install . 
EOF
        )
        ->withPkgName('libjxl');

    $p->addLibrary($lib);
}

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
            --with-zip=yes \
            --with-fontconfig=no \
            --with-heic=no \
            --with-lcms=no \
            --with-lqr=no \
            --with-openexr=no \
            --with-openjp2=no \
            --with-pango=no \
            --with-jpeg=yes \
            --with-png=yes \
            --with-webp=yes \
            --with-raw=no \
            --with-tiff=no \
            --with-zstd=yes \
            --with-lzma=yes \
            --with-xml=yes \
            --with-zip=yes \
            --with-zlib=yes \
            --with-zstd=yes \
            --with-freetype=yes 

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
