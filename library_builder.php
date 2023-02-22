<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;
// ================================================================================================
// Library
// ================================================================================================

/**
cmake use static openssl

set(OPENSSL_USE_STATIC_LIBS TRUE)
find_package(OpenSSL REQUIRED)
target_link_libraries(program OpenSSL::Crypto)
 */

function install_openssl(Preprocessor $p)
{
    $p->addLibrary((new Library('openssl'))
        ->withUrl('https://www.openssl.org/source/openssl-1.1.1p.tar.gz')
        ->withPrefix(OPENSSL_PREFIX)
        ->withConfigure('./config' . ($p->getOsType() === 'macos' ? '' : ' -static --static') . ' no-shared --prefix=' . OPENSSL_PREFIX)
        ->withMakeInstallCommand('install_sw')
        ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
        ->withHomePage('https://www.openssl.org/')
        ->withPkgName('openssl')
    );
}

function install_libiconv(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libiconv'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
            ->withPrefix(ICONV_PREFIX)
            ->withPkgConfig('')
            ->withConfigure('./configure --prefix=' . ICONV_PREFIX . ' enable_static=yes enable_shared=no')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
    );
}

// MUST be in the /usr directory
// Dependent libiconv
function install_libxml2(Preprocessor $p)
{
    $libxml2_prefix = LIBXML2_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libxml2'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxml2/-/archive/v2.9.10/libxml2-v2.9.10.tar.gz')
            ->withPrefix(LIBXML2_PREFIX)
            ->withConfigure(<<<EOF
./autogen.sh && ./configure --prefix=$libxml2_prefix --with-iconv=$iconv_prefix --enable-static=yes --enable-shared=no --without-python
EOF
            )
            ->withPkgName('libxml-2.0')
            ->withLicense('https://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->depends('libiconv')
    );
}

// Dependent libxml2
function install_libxslt(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libxslt'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxslt/-/archive/v1.1.34/libxslt-v1.1.34.tar.gz')
            ->withPrefix(LIBXSLT_PREFIX)
            ->withConfigure('./autogen.sh && ./configure --prefix=' . LIBXSLT_PREFIX . '--enable-static=yes --enable-shared=no')
            ->withLicense('http://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->withPkgName('libexslt libxslt')
            ->depends('libxml2', 'libiconv')
    );
}

function install_imagemagick(Preprocessor $p)
{
    $imagemagick_prefix = IMAGEMAGICK_PREFIX;
    $p->addLibrary(
        (new Library('imagemagick'))
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.0-62.tar.gz')
            ->withFile('ImageMagick-v7.1.0-62.tar.gz')
            ->withPrefix(IMAGEMAGICK_PREFIX)
            ->withConfigure(<<<EOF
              ./configure \
              --prefix={$imagemagick_prefix} \
              --enable-static\
              --disable-shared \
              --with-zip=yes \
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
              --with-jpeg=yes \
              --with-freetype=yes
EOF
            )
            ->withPkgName('ImageMagick')
            ->withLicense('https://imagemagick.org/script/license.php', Library::LICENSE_APACHE2)
            ->depends('libxml2', 'zip', 'zlib', 'libjpeg', 'freetype', 'libwebp', 'libpng', 'libgif')
    );
}

function install_gmp(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('gmp'))
            ->withUrl('https://gmplib.org/download/gmp/gmp-6.2.1.tar.lz')
            ->withPrefix(GMP_PREFIX)
            ->withConfigure('./configure --prefix=' . GMP_PREFIX . ' --enable-static --disable-shared')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withPkgName('gmp')
    );
}

function install_libgif(Preprocessor $p)
{
    $gif_prefix = GIF_PREFIX;
    $p->addLibrary(
        (new Library('libgif'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
            ->withLicense('https://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
            ->withPrefix(GIF_PREFIX)
            ->withMakeOptions('libgif.a')
            ->withMakeInstallCommand('')
            ->withScriptAfterInstall(<<<EOF
                if [ ! -d {$gif_prefix}/lib ]; then
                    mkdir -p {$gif_prefix}/lib
                fi
                if [ ! -d {$gif_prefix}/include ]; then
                    mkdir -p {$gif_prefix}/include
                fi
                cp libgif.a {$gif_prefix}/lib/libgif.a
                cp gif_lib.h {$gif_prefix}/include/gif_lib.h
                EOF
            )
            ->withLdflags('-L' . GIF_PREFIX . '/lib')
            ->withPkgName('')
            ->withPkgConfig('')
    );
    if(0){
        $p->addLibrary(
            (new Library('giflib'))
                ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
                ->withLicense('http://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
                ->withCleanBuildDirectory()
                ->withPrefix('/usr/giflib')
                ->withScriptBeforeConfigure('
    
                default_prefix_dir="/ u s r" # 阻止 macos 系统下编译路径被替换
                # 替换空格
                default_prefix_dir=$(echo "$default_prefix_dir" | sed -e "s/[ ]//g")
                
                sed -i.bakup "s@PREFIX = $default_prefix_dir/local@PREFIX = /usr/giflib@" Makefile
           
                cat >> Makefile <<"EOF"
install-lib-static:
    $(INSTALL) -d "$(DESTDIR)$(LIBDIR)"
    $(INSTALL) -m 644 libgif.a "$(DESTDIR)$(LIBDIR)/libgif.a"
EOF
              
               
                ')
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
    $p->addLibrary(
        (new Library('libpng'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withLicense('http://www.libpng.org/pub/png/src/libpng-LICENSE.txt', Library::LICENSE_SPEC)
            ->withPrefix(PNG_PREFIX)
            ->withConfigure(
                './configure --prefix=' . PNG_PREFIX . ' --enable-static --disable-shared ' .
                '--with-zlib-prefix=' . ZLIB_PREFIX . '  --with-binconfigs'
            )
            ->withPkgName('libpng16')
            ->depends('zlib')
    );
}

function install_libjpeg(Preprocessor $p)
{
    $lib = new Library('libjpeg');
    $lib->withHomePage('https://libjpeg-turbo.org/')
        ->withLicense('https://github.com/libjpeg-turbo/libjpeg-turbo/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
        ->withFile('libjpeg-turbo-2.1.2.tar.gz')
        ->withPrefix(JPEG_PREFIX)
        ->withConfigure('cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=' . JPEG_PREFIX . ' .')
        ->withPkgName('libjpeg');

    // linux 系统中是保存在 /usr/lib64 目录下的，而 macos 是放在 /usr/lib 目录中的，不清楚这里是什么原因？
    $jpeg_lib_dir = JPEG_PREFIX . '/' . ($p->getOsType() === 'macos' ? 'lib' : 'lib64');

    $lib->withLdflags('-L' . $jpeg_lib_dir)
        ->withPkgConfig($jpeg_lib_dir . '/pkgconfig');
    if ($p->getOsType() === 'macos') {
        $lib->withScriptAfterInstall('find ' . $lib->prefix . ' -name \*.dylib | xargs rm -f');
    }
    $p->addLibrary($lib);
}

function install_freetype(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('freetype'))
            ->withPrefix(FREETYPE_PREFIX)
            ->withUrl('https://download.savannah.gnu.org/releases/freetype/freetype-2.10.4.tar.gz')
            ->withLicense('https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/FTL.TXT', Library::LICENSE_SPEC)
            ->withConfigure(
                'export BZIP2_CFLAGS="-I' . BZIP2_PREFIX . '/include" ' .PHP_EOL.
                'export BZIP2_LIBS="-L' . BZIP2_PREFIX . '/lib -lbz2" ' .PHP_EOL.
                'export PATH="' . PNG_PREFIX . '/bin:$PATH" ' .PHP_EOL .
                './configure --prefix=' . FREETYPE_PREFIX . ' \\' . PHP_EOL .
                '--enable-static \\' . PHP_EOL .
                '--disable-shared \\' . PHP_EOL .
                '--with-zlib=yes \\' . PHP_EOL .
                '--with-bzip2=yes \\' . PHP_EOL .
                '--with-png=yes \\' . PHP_EOL .
                '--with-harfbuzz=no \\' . PHP_EOL .
                '--with-brotli=no' . PHP_EOL
            )
            ->withHomePage('https://freetype.org/')
            ->withPkgName('freetype2')
            ->depends('zlib', 'libpng')
    );

}

function install_libwebp(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libwebp'))
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withHomePage('https://github.com/webmproject/libwebp')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
            ->withPrefix(WEBP_PREFIX)
            ->withConfigure('./autogen.sh && ./configure --prefix=' . WEBP_PREFIX . ' --enable-static --disable-shared ' .
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
}

function install_sqlite3(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('sqlite3'))
            ->withUrl('https://www.sqlite.org/2021/sqlite-autoconf-3370000.tar.gz')
            ->withPrefix(SQLITE3_PREFIX)
            ->withConfigure('./configure --prefix=' . SQLITE3_PREFIX . ' --enable-static --disable-shared')
            ->withHomePage('https://www.sqlite.org/index.html')
            ->withLicense('https://www.sqlite.org/copyright.html', Library::LICENSE_SPEC)
            ->withPkgName('sqlite3')
    );
}

function install_zlib(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('zlib'))
            ->withUrl('https://udomain.dl.sourceforge.net/project/libpng/zlib/1.2.11/zlib-1.2.11.tar.gz')
            ->withPrefix(ZLIB_PREFIX)
            ->withConfigure(
                <<<EOF
./configure -help 

EOF
.
                './configure --prefix=' . ZLIB_PREFIX . ' --static'
            )
            ->withHomePage('https://zlib.net/')
            ->withLicense('https://zlib.net/zlib_license.html', Library::LICENSE_SPEC)
            ->withPkgName('zlib')
    );
}

function install_bzip2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('bzip2'))
            ->withUrl('https://sourceware.org/pub/bzip2/bzip2-1.0.8.tar.gz')
            ->withPrefix(BZIP2_PREFIX)
            ->withMakeOptions('PREFIX=' . BZIP2_PREFIX)
            ->withMakeInstallOptions('PREFIX=' . BZIP2_PREFIX)
            ->withHomePage('https://www.sourceware.org/bzip2/')
            ->withLicense('https://www.sourceware.org/bzip2/', Library::LICENSE_BSD)
    );
}

function install_icu(Preprocessor $p)
{
    $icu_prefix = ICU_PREFIX;
    $os = $p->getOsType() == 'macos' ? 'MacOSX' : 'Linux';
    $p->addLibrary(
        (new Library('icu'))
            ->withUrl('https://github.com/unicode-org/icu/releases/download/release-60-3/icu4c-60_3-src.tgz')
            ->withManual("https://unicode-org.github.io/icu/userguide/icu4c/build.html")
            ->withCleanBuildDirectory()
            ->withPrefix(ICU_PREFIX)
            ->withConfigure(<<<EOF
             export CPPFLAGS="-DU_CHARSET_IS_UTF8=1  -DU_USING_ICU_NAMESPACE=1  -DU_STATIC_IMPLEMENTATION=1"
             source/runConfigureICU $os --prefix={$icu_prefix} \
             --enable-icu-config=yes \
             --enable-static=yes \
             --enable-shared=no \
             --with-data-packaging=archive \
             --enable-release=yes \
             --enable-extras=yes \
             --enable-icuio=yes \
             --enable-dyload=no \
             --enable-tools=yes \
             --enable-tests=no \
             --enable-samples=no
EOF
            )
            ->withPkgName('icu-i18n  icu-io   icu-uc')
            ->withHomePage('https://icu.unicode.org/')
            ->withLicense('https://github.com/unicode-org/icu/blob/main/icu4c/LICENSE', Library::LICENSE_SPEC)
    );
}

function install_oniguruma(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('oniguruma'))
            ->withUrl('https://codeload.github.com/kkos/oniguruma/tar.gz/refs/tags/v6.9.7')
            ->withPrefix(ONIGURUMA_PREFIX)
            ->withConfigure('./autogen.sh && ./configure --prefix=' . ONIGURUMA_PREFIX . ' --enable-static --disable-shared')
            ->withFile('oniguruma-6.9.7.tar.gz')
            ->withLicense('https://github.com/kkos/oniguruma/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withPkgName('oniguruma')
    );
}

// MUST be in the /usr directory
function install_zip(Preprocessor $p)
{
    $openssl_prefix = OPENSSL_PREFIX;
    $zip_prefix = ZIP_PREFIX;
    $liblzma_prefix = LIBLZ4_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('zip'))
            //->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
            ->withUrl('https://libzip.org/download/libzip-1.9.2.tar.gz')
            ->withManual('https://libzip.org')
            ->withPrefix($zip_prefix)
            ->withCleanBuildDirectory()
            ->withCleanInstallDirectory($zip_prefix)
            ->withConfigure(<<<EOF
            cmake -Wno-dev .  \
            -DCMAKE_INSTALL_PREFIX={$zip_prefix} \
            -DCMAKE_BUILD_TYPE=optimized \
            -DBUILD_TOOLS=OFF \
            -DBUILD_EXAMPLES=OFF \
            -DBUILD_DOC=OFF \
            -DLIBZIP_DO_INSTALL=ON \
            -DBUILD_SHARED_LIBS=OFF \
            -DENABLE_GNUTLS=OFF  \
            -DENABLE_MBEDTLS=OFF \
            -DENABLE_OPENSSL=ON \
            -DOPENSSL_USE_STATIC_LIBS=TRUE \
            -DOPENSSL_LIBRARIES={$openssl_prefix}/lib \
            -DOPENSSL_INCLUDE_DIR={$openssl_prefix}/include \
            -DZLIB_LIBRARY={$zlib_prefix}/lib \
            -DZLIB_INCLUDE_DIR={$zlib_prefix}/include \
            -DENABLE_BZIP2=ON \
            -DBZIP2_LIBRARIES={$bzip2_prefix}/lib \
            -DBZIP2_LIBRARY={$bzip2_prefix}/lib \
            -DBZIP2_INCLUDE_DIR={$bzip2_prefix}/include \
            -DBZIP2_NEED_PREFIX=ON \
            -DENABLE_LZMA=ON  \
            -DLIBLZMA_LIBRARY={$liblzma_prefix}/lib \
            -DLIBLZMA_INCLUDE_DIR={$liblzma_prefix}/include \
            -DLIBLZMA_HAS_AUTO_DECODER=ON  \
            -DLIBLZMA_HAS_EASY_ENCODER=ON  \
            -DLIBLZMA_HAS_LZMA_PRESET=ON \
            -DENABLE_ZSTD=ON \
            -DZstd_LIBRARY={$libzstd_prefix}/lib \
            -DZstd_INCLUDE_DIR={$libzstd_prefix}/include
EOF

            )
            ->withMakeOptions('VERBOSE=1')
            ->withPkgName('libzip')
            ->withHomePage('https://libzip.org/')
            ->withLicense('https://libzip.org/license/', Library::LICENSE_BSD)
            ->depends('openssl', 'zlib', 'bzip2','liblzma','libzstd')
    );
}

function install_cares(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('cares'))
            ->withUrl('https://c-ares.org/download/c-ares-1.19.0.tar.gz')
            ->withPrefix(CARES_PREFIX)
            ->withConfigure('./configure --prefix=' . CARES_PREFIX . ' --enable-static --disable-shared')
            ->withPkgName('libcares')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withHomePage('https://c-ares.org/')
    );
}

function install_readline(Preprocessor $p)
{
    $readline_prefix = READLINE_PREFIX;
    $p->addLibrary(
        (new Library('readline'))
            ->withUrl('https://ftp.gnu.org/gnu/readline/readline-8.2.tar.gz')
            ->withMirrorUrl('https://mirrors.tuna.tsinghua.edu.cn/gnu/readline/readline-8.2.tar.gz')
            ->withMirrorUrl('https://mirrors.ustc.edu.cn/gnu/readline/readline-8.2.tar.gz')
            ->withPrefix(READLINE_PREFIX)
            ->withConfigure(<<<EOF
                ./configure \
                --prefix={$readline_prefix} \
                --enable-static \
                --disable-shared \
                --with-curses \
                --enable-multibyte
EOF
            )
            ->withPkgName('readline')
            ->withLdflags('-L' . READLINE_PREFIX . '/lib')
            ->withLicense('https://www.gnu.org/licenses/gpl.html', Library::LICENSE_GPL)
            ->withHomePage('https://tiswww.case.edu/php/chet/readline/rltop.html')
            ->depends('ncurses')
    );
}

function install_libedit(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libedit'))
            ->withUrl('https://thrysoee.dk/editline/libedit-20210910-3.1.tar.gz')
            ->withPrefix(LIBEDIT_PREFIX)
            ->withConfigure('./configure --prefix=' . LIBEDIT_PREFIX . ' --enable-static --disable-shared')
            ->withLdflags('')
            ->withLicense('http://www.netbsd.org/Goals/redistribution.html', Library::LICENSE_BSD)
            ->withHomePage('https://thrysoee.dk/editline/')
    );
}

function install_ncurses(Preprocessor $p)
{
    $ncurses_prefix = NCURSES_PREFIX;
    $p->addLibrary(
        (new Library('ncurses'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withMirrorUrl('https://mirrors.tuna.tsinghua.edu.cn/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withMirrorUrl('https://mirrors.ustc.edu.cn/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withPrefix(NCURSES_PREFIX)
            ->withConfigure(<<<EOF
            mkdir -p {$ncurses_prefix}/lib/pkgconfig
            ./configure \
            --prefix={$ncurses_prefix} \
            --enable-static \
            --disable-shared \
            --enable-pc-files \
            --with-pkg-config={$ncurses_prefix}/lib/pkgconfig \
            --with-pkg-config-libdir={$ncurses_prefix}/lib/pkgconfig \
            --with-normal \
            --enable-widec \
            --enable-echo \
            --with-ticlib  \
            --without-termlib \
            --enable-sp-funcs \
            --enable-term-driver \
            --enable-ext-colors \
            --enable-ext-mouse \
            --enable-ext-putwin \
            --enable-no-padding \
            --without-debug \
            --without-tests \
            --without-dlsym \
            --without-debug \
            --enable-symlinks
EOF
            )
            ->withScriptBeforeInstall('
                ln -s ' . NCURSES_PREFIX . '/lib/pkgconfig/formw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/form.pc ;
                ln -s ' . NCURSES_PREFIX . '/lib/pkgconfig/menuw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/menu.pc ;
                ln -s ' . NCURSES_PREFIX . '/lib/pkgconfig/ncurses++w.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/ncurses++.pc ;
                ln -s ' . NCURSES_PREFIX . '/lib/pkgconfig/ncursesw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/ncurses.pc ;
                ln -s ' . NCURSES_PREFIX . '/lib/pkgconfig/panelw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/panel.pc ;
                ln -s ' . NCURSES_PREFIX . '/lib/pkgconfig/ticw.pc ' . NCURSES_PREFIX . '/lib/pkgconfig/tic.pc ;

                ln -s ' . NCURSES_PREFIX . '/lib/libformw.a ' . NCURSES_PREFIX . '/lib/libform.a ;
                ln -s ' . NCURSES_PREFIX . '/lib/libmenuw.a ' . NCURSES_PREFIX . '/lib/libmenu.a ;
                ln -s ' . NCURSES_PREFIX . '/lib/libncurses++w.a ' . NCURSES_PREFIX . '/lib/libncurses++.a ;
                ln -s ' . NCURSES_PREFIX . '/lib/libncursesw.a ' . NCURSES_PREFIX . '/lib/libncurses.a ;
                ln -s ' . NCURSES_PREFIX . '/lib/libpanelw.a  ' . NCURSES_PREFIX . '/lib/libpanel.a ;
                ln -s ' . NCURSES_PREFIX . '/lib/libticw.a ' . NCURSES_PREFIX . '/lib/libtic.a ;
            ')
            ->withPkgName('ncursesw')
            ->withLicense('https://github.com/projectceladon/libncurses/blob/master/README', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/projectceladon/libncurses')
    );
}


function install_libsodium(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libsodium'))
            // ISC License, like BSD
            ->withLicense('https://en.wikipedia.org/wiki/ISC_license', Library::LICENSE_SPEC)
            ->withHomePage('https://doc.libsodium.org/')
            ->withUrl('https://download.libsodium.org/libsodium/releases/libsodium-1.0.18.tar.gz')
            ->withPrefix(LIBSODIUM_PREFIX)
            ->withConfigure('./configure --prefix=' . LIBSODIUM_PREFIX . ' --enable-static --disable-shared')
            ->withPkgName('libsodium')
    );
}

function install_libyaml(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libyaml'))
            ->withUrl('https://pyyaml.org/download/libyaml/yaml-0.2.5.tar.gz')
            ->withPrefix(LIBYAML_PREFIX)
            ->withConfigure('./configure --prefix=' . LIBYAML_PREFIX . ' --enable-static --disable-shared')
            ->withPkgName('yaml-0.1')
            ->withLicense('https://pyyaml.org/wiki/LibYAML', Library::LICENSE_MIT)
            ->withHomePage('https://pyyaml.org/wiki/LibYAML')
    );
}

function install_brotli(Preprocessor $p)
{
    /*
    -DCMAKE_BUILD_TYPE="${BUILD_TYPE}" \
    -DCMAKE_INSTALL_PREFIX="${PREFIX}" \
    -DCMAKE_INSTALL_LIBDIR="${LIBDIR}" \
  */
    $p->addLibrary(
        (new Library('brotli'))
            ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/google/brotli')
            ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
            ->withManual('https://github.com/google/brotli/')
            ->withFile('brotli-1.0.9.tar.gz')
            ->withPrefix(BROTLI_PREFIX)
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure('
            test -d /usr/brotli && rm -rf /usr/brotli
            ')
            ->withConfigure('
            mkdir cmake-build 
            cd cmake-build
            cmake -DCMAKE_BUILD_TYPE=Release -DBUILD_SHARED_LIBS=OFF -DCMAKE_INSTALL_PREFIX=' . BROTLI_PREFIX . ' .. '. PHP_EOL.
                <<<EOF
            cmake --build . --config Release --target install
           
EOF

            )
            ->withSkipMakeAndMakeInstall()
            ->withScriptAfterInstall(
                implode(PHP_EOL, [
                    'rm -rf ' . BROTLI_PREFIX . '/lib/*.so.*',
                    'rm -rf ' . BROTLI_PREFIX . '/lib/*.so',
                    'rm -rf ' . BROTLI_PREFIX . '/lib/*.dylib',
                    'mv ' . BROTLI_PREFIX . '/lib/libbrotlicommon-static.a ' . BROTLI_PREFIX . '/lib/libbrotli.a',
                    'mv ' . BROTLI_PREFIX . '/lib/libbrotlienc-static.a ' . BROTLI_PREFIX . '/lib/libbrotlienc.a',
                    'mv ' . BROTLI_PREFIX . '/lib/libbrotlidec-static.a ' . BROTLI_PREFIX . '/lib/libbrotlidec.a',
                ]))
            ->withPkgName('libbrotlicommon libbrotlidec libbrotlienc')

    );
}

/**

-lz      压缩库（Z）

-lrt     实时库（real time）：shm_open系列

-lm     数学库（math）

-lc     标准C库（C lib）

-dl ，是显式加载动态库的动态函数库

 *
 */
/**
cur  交叉编译
 *
https://curl.se/docs/install.html
 *
export PATH=$PATH:/opt/hardhat/devkit/ppc/405/bin
export CPPFLAGS="-I/opt/hardhat/devkit/ppc/405/target/usr/include"
export AR=ppc_405-ar
export AS=ppc_405-as
export LD=ppc_405-ld
export RANLIB=ppc_405-ranlib
export CC=ppc_405-gcc
export NM=ppc_405-nm
--with-random=/dev/urandom
 *
randlib
strip
 *
 */
function install_curl(Preprocessor $p)
{
    //http3 有多个实现
    //参考文档： https://curl.se/docs/http3.html
    //https://curl.se/docs/protdocs.html
    $curl_prefix = CURL_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX ;

    $libidn2_prefix = LIBIDN2_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $cares_prefix = CARES_PREFIX;
    $brotli_prefix = BROTLI_PREFIX;
    $nghttp2_prefix = NGHTTP2_PREFIX;
    $p->addLibrary(
        (new Library('curl'))
            ->withUrl('https://curl.se/download/curl-7.88.0.tar.gz')
            ->withManual('https://curl.se/docs/install.html')
            ->withCleanBuildDirectory()
            ->withPrefix($curl_prefix)
            ->withCleanInstallDirectory($curl_prefix)
            ->withConfigure(<<<EOF
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static zlib libbrotlicommon  libbrotlidec  libbrotlienc openssl libcares libidn2 )" \
            LDFLAGS="$(pkg-config --libs-only-L      --static zlib libbrotlicommon  libbrotlidec  libbrotlienc openssl libcares libidn2 )" \
            LIBS="$(pkg-config --libs-only-l         --static zlib libbrotlicommon  libbrotlidec  libbrotlienc openssl libcares libidn2 )" \
            ./configure --prefix={$curl_prefix}  \
            --enable-static --disable-shared \
            --without-librtmp --disable-ldap --disable-rtsp \
            --enable-http --enable-alt-svc --enable-hsts --enable-http-auth --enable-mime --enable-cookies \
            --enable-doh --enable-threaded-resolver --enable-ipv6 --enable-proxy  \
            --enable-websockets --enable-get-easy-options \
            --enable-file --enable-mqtt --enable-unix-sockets  --enable-progress-meter \
            --enable-optimize \
            --with-zlib={$zlib_prefix} \
            --with-openssl={$openssl_prefix} \
            --with-libidn2={$libidn2_prefix} \
            --with-zstd={$libzstd_prefix} \
            --enable-ares={$cares_prefix} \
            --with-brotli={$brotli_prefix} \
            --with-default-ssl-backend=openssl \
            --without-nghttp2 \
            --without-ngtcp2 \
            --without-nghttp3 
            
            #--with-gnutls=GNUTLS_PREFIX
            #--with-nghttp3=NGHTTP3_PREFIX
            #--with-ngtcp2=NGTCP2_PREFIX 
            #--with-nghttp2=NGHTTP2_PREFIX 
            #--without-brotli
            #--disable-ares
            
            #--with-ngtcp2=/usr/ngtcp2 \
            #--with-quiche=/usr/quiche 
            #--with-msh3=PATH     
            
EOF
            )
            ->withPkgName('libcurl')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withHomePage('https://curl.se/')
            ->depends('openssl', 'cares', 'zlib','brotli','libzstd','libidn2')
    );
    /**
    configure: pkg-config: SSL_LIBS: "-lssl -lcrypto"
    configure: pkg-config: SSL_LDFLAGS: "-L/usr/openssl/lib"
    configure: pkg-config: SSL_CPPFLAGS: "-I/usr/openssl/include"

    onfigure: pkg-config: IDN_LIBS: "-lidn2"
    configure: pkg-config: IDN_LDFLAGS: "-L/usr/libidn2/lib"
    configure: pkg-config: IDN_CPPFLAGS: "-I/usr/libidn2/include"
    configure: pkg-config: IDN_DIR: "/usr/libidn2/lib"

    configure: -l is -lnghttp2
    configure: -I is -I/usr/nghttp2/include
    configure: -L is -L/usr/nghttp2/lib
    # search idn2_lookup_ul
     *

    configure: pkg-config: ares LIBS: "-lcares"
    configure: pkg-config: ares LDFLAGS: "-L/usr/cares/lib"
    configure: pkg-config: ares CPPFLAGS: "-I/usr/cares/include"

     * -lidn -lrt

     */
}

function install_mimalloc(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('mimalloc'))
            ->withUrl('https://github.com/microsoft/mimalloc/archive/refs/tags/v2.0.7.tar.gz')
            ->withFile('mimalloc-2.0.7.tar.gz')
            ->withPrefix(MIMALLOC_PREFIX)
            ->withConfigure('cmake . -DMI_BUILD_SHARED=OFF -DCMAKE_INSTALL_PREFIX=' . MIMALLOC_PREFIX . ' -DMI_INSTALL_TOPLEVEL=ON -DMI_PADDING=OFF -DMI_SKIP_COLLECT_ON_EXIT=ON -DMI_BUILD_TESTS=OFF')
            ->withPkgName('libmimalloc')
            ->withLicense('https://github.com/microsoft/mimalloc/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://microsoft.github.io/mimalloc/')
            ->withLdflags('-L' . MIMALLOC_PREFIX . '/lib -lmimalloc')
    );
}

function install_pgsql(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('pgsql'))
            ->withHomePage('https://www.postgresql.org/')
            ->withLicense('https://www.postgresql.org/about/licence/', Library::LICENSE_SPEC)
            ->withUrl('https://ftp.postgresql.org/pub/source/v15.1/postgresql-15.1.tar.gz')
            //https://www.postgresql.org/docs/devel/installation.html
            //https://www.postgresql.org/docs/devel/install-make.html#INSTALL-PROCEDURE-MAKE
            ->withManual('https://www.postgresql.org/docs/')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                '
               test -d /usr/pgsql && rm -rf /usr/pgsql
            '
            )
            ->withConfigure(
                '
            ./configure --help
            
            sed -i.backup "s/invokes exit\'; exit 1;/invokes exit\';/"  src/interfaces/libpq/Makefile
  
            # 替换指定行内容
            sed -i.backup "102c all: all-lib" src/interfaces/libpq/Makefile
           
            # export CPPFLAGS="-static -fPIE -fPIC -O2 -Wall "
            # export CFLAGS="-static -fPIE -fPIC -O2 -Wall "
            
            export CPPFLAGS=$(pkg-config  --cflags --static  icu-uc icu-io icu-i18n readline libxml-2.0)
            export LIBS=$(pkg-config  --libs --static   icu-uc icu-io icu-i18n readline libxml-2.0)
          
         
            ./configure  --prefix=/usr/pgsql \
            --enable-coverage=no \
            --with-ssl=openssl  \
            --with-readline \
            --with-icu \
            --without-ldap \
            --with-libxml  \
            --with-libxslt \
            --with-includes="/usr/openssl/include/:/usr/libxml2/include/:/usr/libxslt/include:/usr/readline/include/readline:/usr/icu/include:/usr/zlib/include:/usr/include" \
            --with-libraries="/usr/openssl/lib:/usr/libxml2/lib/:/usr/libxslt/lib/:/usr/readline/lib:/usr/icu/lib:/usr/zlib/lib:/usr/lib"

            make -C src/include install 
            result_code=$?
            [[ $result_code -ne 0 ]] && echo "[make FAILURE]" && exit $result_code;
            
            make -C  src/bin/pg_config install
            result_code=$?
            [[ $result_code -ne 0 ]] && echo "[make FAILURE]" && exit $result_code;
            
            
            make -C  src/common -j $cpu_nums all 
            make -C  src/common install 
            result_code=$?
            [[ $result_code -ne 0 ]] && echo "[make FAILURE]" && exit $result_code;
            
            make -C  src/port -j $cpu_nums all 
            make -C  src/port install 
            result_code=$?
            [[ $result_code -ne 0 ]] && echo "[make FAILURE]" && exit $result_code;
                        
            make -C  src/backend/libpq -j $cpu_nums all 
            make -C  src/backend/libpq install 
            result_code=$?
            [[ $result_code -ne 0 ]] && echo "[make FAILURE]" && exit $result_code;
                        
            make -C src/interfaces/ecpg   -j $cpu_nums all-pgtypeslib-recurse all-ecpglib-recurse all-compatlib-recurse all-preproc-recurse
            make -C src/interfaces/ecpg  install-pgtypeslib-recurse install-ecpglib-recurse install-compatlib-recurse install-preproc-recurse
            result_code=$?
            [[ $result_code -ne 0 ]] && echo "[make FAILURE]" && exit $result_code;
                        
            # 静态编译 src/interfaces/libpq/Makefile  有静态配置  参考： all-static-lib
            
            make -C src/interfaces/libpq  -j $cpu_nums # soname=true
            make -C src/interfaces/libpq  install 
            result_code=$?
            [[ $result_code -ne 0 ]] && echo "[make FAILURE]" && exit $result_code;
                        
            rm -rf /usr/pgsql/lib/*.so.*
            rm -rf /usr/pgsql/lib/*.so
            return 0 

            '
            )
            ->withPkgName('libpq')
            ->withPkgConfig('/usr/pgsql/lib/pkgconfig')
            ->withLdflags('-L/usr/pgsql/lib/')
            ->withBinPath('/usr/pgsql/bin/')
    );
}

function install_libffi($p)
{
    $p->addLibrary(
        (new Library('libffi'))
            ->withHomePage('https://sourceware.org/libffi/')
            ->withLicense('http://github.com/libffi/libffi/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/libffi/libffi/releases/download/v3.4.4/libffi-3.4.4.tar.gz')
            ->withFile('libffi-3.4.4.tar.gz')
            ->withPrefix('/usr/libffi/')
            ->withScriptBeforeConfigure(
                'test -d /usr/libffi && rm -rf /usr/libffi'
            )
            ->withConfigure(
                '
            ./configure --help ;
            ./configure \
            --prefix=/usr/libffi \
            --enable-shared=no \
            --enable-static=yes 
            '
            )
            ->withPkgName('libffi')
            ->withPkgConfig('/usr/libffi/lib/pkgconfig')
            ->withLdflags('-L/usr/libffi/lib/')
            ->withBinPath('/usr/libffi/bin/')
    //->withSkipInstall()
    //->disablePkgName()
    //->disableDefaultPkgConfig()
    //->disableDefaultLdflags()
    );
}

function install_fastdfs($p)
{
    $p->addLibrary(
        (new Library('fastdfs'))
            ->withHomePage('https://github.com/happyfish100/fastdfs.git')
            ->withLicense('https://github.com/happyfish100/fastdfs/blob/master/COPYING-3_0.txt', Library::LICENSE_GPL)
            ->withUrl('https://github.com/happyfish100/fastdfs/archive/refs/tags/V6.9.4.tar.gz')
            ->withFile('fastdfs-V6.9.4.tar.gz')
            ->withPrefix('/usr/fastdfs/')
            ->withScriptBeforeConfigure(
                'test -d /usr/fastdfs/ && rm -rf /usr/fastdfs/'
            )
            ->withConfigure(
                '
            export DESTDIR=/usr/libserverframe/
            ./make.sh clean && ./make.sh && ./make.sh install
            ./setup.sh /etc/fdfs
            '
            )
            ->withPkgName('')
            ->withPkgConfig('/usr/fastdfs//lib/pkgconfig')
            ->withLdflags('-L/usr/fastdfs/lib/')
            ->withBinPath('/usr/fastdfs/bin/')
            ->withSkipBuildInstall()
    //->withSkipInstall()
    //->disablePkgName()
    //->disableDefaultPkgConfig()
    //->disableDefaultLdflags()
    );
}

function install_libserverframe($p)
{
    $p->addLibrary(
        (new Library('libserverframe'))
            ->withHomePage('https://github.com/happyfish100/libserverframe')
            ->withLicense('https://github.com/happyfish100/libserverframe/blob/master/LICENSE', Library::LICENSE_GPL)
            ->withUrl('https://github.com/happyfish100/libserverframe/archive/refs/tags/V1.1.25.tar.gz')
            ->withFile('libserverframe-V1.1.25.tar.gz')
            ->withPrefix('/usr/libserverframe/')
            ->withScriptBeforeConfigure(
                'test -d /usr/libserverframe/ && rm -rf /usr/libserverframe/'
            )
            ->withConfigure(
                '
                export DESTDIR=/usr/libserverframe/
                ./make.sh clean && ./make.sh && ./make.sh install
            '
            )
            ->withPkgName('')
            ->withSkipBuildInstall()
    //->disablePkgName()
    //->disableDefaultPkgConfig()
    //->disableDefaultLdflags()
    );
}
function install_libfastcommon($p)
{
    $p->addLibrary(
        (new Library('libfastcommon'))
            ->withHomePage('https://github.com/happyfish100/libfastcommon')
            ->withLicense('https://github.com/happyfish100/libfastcommon/blob/master/LICENSE', Library::LICENSE_GPL)
            ->withUrl('https://github.com/happyfish100/libfastcommon/archive/refs/tags/V1.0.66.tar.gz')
            ->withFile('libfastcommon-V1.0.66.tar.gz')
            ->withPrefix('/usr/libfastcommon/')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                'test -d /usr/libfastcommon/ && rm -rf /usr/libfastcommon/'
            )
            ->withConfigure(
                '
             export DESTDIR=/usr/libfastcommon
             ./make.sh clean && ./make.sh && ./make.sh install
             exit 0 
            '
            )
            ->withPkgName('')
            ->withPkgConfig('/usr/libfastcommon/usr/lib/pkgconfig')
            ->withLdflags('-L/usr/libfastcommon/usr/lib -L/usr/libfastcommon/usr/lib64')
    //->disablePkgName()
    //->disableDefaultPkgConfig()
    //->disableDefaultLdflags()
    );
}

function install_php_internal_extensions($p)
{

    $workDir = $p->getWorkDir();
    $p->addLibrary(
        (new Library('php_internal_extensions'))
            ->withHomePage('https://www.php.net/')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Library::LICENSE_PHP)
            ->withUrl('https://github.com/php/php-src/archive/refs/tags/php-8.1.12.tar.gz')
            ->withFile('php-8.1.12.tar.gz')
            ->withManual('https://www.php.net/docs.php')
            ->withLabel('php_internal_extension')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                "
                    test -d {$workDir}/ext/ffi && rm -rf {$workDir}/ext/ffi
                    cp -rf  ext/ffi {$workDir}/ext/
                    
                    test -d {$workDir}/ext/pdo_pgsql && rm -rf {$workDir}/ext/pdo_pgsql
                    cp -rf  ext/pdo_pgsql {$workDir}/ext/
                    
                    test -d {$workDir}/ext/pgsql && rm -rf {$workDir}/ext/pgsql
                    cp -rf  ext/pgsql {$workDir}/ext/
                "
            )
            ->withConfigure('return 0')
            ->disablePkgName()
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->withSkipBuildLicense()

    );

}

function install_liblz4(Preprocessor $p)
{
    $liblz4_prefix = LIBLZ4_PREFIX;
    $p->addLibrary(
        (new Library('liblz4'))
            ->withHomePage('http://www.lz4.org')
            ->withLicense('https://github.com/lz4/lz4/blob/dev/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/lz4/lz4/archive/refs/tags/v1.9.4.tar.gz')
            ->withManual('https://github.com/lz4/lz4.git')
            ->withFile('lz4-v1.9.4.tar.gz')
            ->withPkgName('liblz4')
            ->withPrefix($liblz4_prefix)
            ->withCleanBuildDirectory()
            ->withCleanInstallDirectory($liblz4_prefix)
            ->withConfigure(<<<EOF
            cd build/cmake/
            cmake . -DCMAKE_INSTALL_PREFIX={$liblz4_prefix}  -DBUILD_SHARED_LIBS=OFF  -DBUILD_STATIC_LIBS=ON
EOF
            )

    //可以使用CMAKE 编译 也可以
    //不使用CMAKE，需要自己修改安装目录
    //->withMakeOptions('INSTALL_PROGRAM=/usr/liblz4/')
    //->withMakeInstallOptions("DESTDIR=/usr/liblz4/")
    );
}

function install_liblzma(Preprocessor $p)
{
    $liblzma_prefix = LIBLZ4_PREFIX;
    $p->addLibrary(
        (new Library('liblzma'))
            ->withHomePage('https://tukaani.org/xz/')
            ->withLicense('https://github.com/tukaani-project/xz/blob/master/COPYING.GPLv3', Library::LICENSE_LGPL)
            ->withManual('https://github.com/tukaani-project/xz.git')
            //->withUrl('https://tukaani.org/xz/xz-5.2.9.tar.gz')
            //->withFile('xz-5.2.9.tar.gz')
            ->withUrl('https://github.com/tukaani-project/xz/releases/download/v5.4.1/xz-5.4.1.tar.gz')
            ->withFile('xz-5.4.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix($liblzma_prefix)
            ->withCleanInstallDirectory($liblzma_prefix)
            ->withConfigure('./configure --prefix=' .$liblzma_prefix . ' --enable-static  --disable-shared --disable-doc')
            ->withPkgName('liblzma')
    );

}

function install_libzstd(Preprocessor $p)
{
    $libzstd_prefix = LIBZSTD_PREFIX;
    $p->addLibrary(
        (new Library('libzstd'))
            ->withHomePage('https://github.com/facebook/zstd')
            ->withLicense('https://github.com/facebook/zstd/blob/dev/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://github.com/facebook/zstd/releases/download/v1.5.2/zstd-1.5.2.tar.gz')
            ->withFile('zstd-1.5.2.tar.gz')
            ->withPrefix($libzstd_prefix)
            ->withCleanBuildDirectory()
            ->withCleanInstallDirectory($libzstd_prefix)
            ->withConfigure(
                <<<EOF
            mkdir -p build/cmake/builddir
            cd build/cmake/builddir
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libzstd_prefix} \
            -DZSTD_BUILD_STATIC=ON \
            -DCMAKE_BUILD_TYPE=Release \
            -DZSTD_BUILD_CONTRIB=ON \
            -DZSTD_BUILD_PROGRAMS=ON \
            -DZSTD_BUILD_SHARED=OFF \
            -DZSTD_BUILD_TESTS=OFF \
            -DZSTD_LEGACY_SUPPORT=ON 
EOF
            )
            ->withMakeOptions('lib')
            //->withMakeInstallOptions('install PREFIX=/usr/libzstd/')
            ->withPkgName('libzstd')
            ->depends('liblz4')

    );
    $p->addLibrary(
        (new Library('libzstd'))
            ->withHomePage('https://github.com/facebook/zstd')
            ->withLicense('https://github.com/facebook/zstd/blob/dev/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://github.com/facebook/zstd/releases/download/v1.5.2/zstd-1.5.2.tar.gz')
            ->withFile('zstd-1.5.2.tar.gz')
            ->withPrefix('/usr/libzstd/')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                '
            test -d /usr/libzstd/ && rm -rf /usr/libzstd/
           
            '
            )
            ->withConfigure(
                '
            mkdir -p build/cmake/builddir
            cd build/cmake/builddir
            # cmake -LH ..
            cmake .. \
            -DCMAKE_INSTALL_PREFIX=/usr/libzstd/ \
            -DZSTD_BUILD_STATIC=ON \
            -DCMAKE_BUILD_TYPE=Release \
            -DZSTD_BUILD_CONTRIB=ON \
            -DZSTD_BUILD_PROGRAMS=OFF \
            -DZSTD_BUILD_SHARED=OFF \
            -DZSTD_BUILD_TESTS=OFF \
            -DZSTD_LEGACY_SUPPORT=ON \
            \
            -DZSTD_ZLIB_SUPPORT=ON \
            -DZLIB_INCLUDE_DIR=/usr/zlib/include \
            -DZLIB_LIBRARY=/usr/zlib/lib \
            \
            -DZSTD_LZ4_SUPPORT=ON \
            -DLIBLZ4_INCLUDE_DIR=/usr/liblz4/include \
            -DLIBLZ4_LIBRARY=/usr/liblz4/lib \
            \
            -DZSTD_LZMA_SUPPORT=ON \
            -DLIBLZMA_LIBRARY=/usr/liblzma/lib \
            -DLIBLZMA_INCLUDE_DIR=/usr/liblzma/include \
            -DLIBLZMA_HAS_AUTO_DECODER=ON\
            -DLIBLZMA_HAS_EASY_ENCODER=ON \
            -DLIBLZMA_HAS_LZMA_PRESET=ON
            '
            )
            ->withMakeOptions('lib')
            ->withMakeInstallOptions('install PREFIX=/usr/libzstd/')
            ->withPkgName('libzstd')


    );
}

function install_harfbuzz(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('harfbuzz'))

            ->withLicense('https://github.com/harfbuzz/harfbuzz/blob/main/COPYING', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/harfbuzz/harfbuzz.git')
            ->withUrl('https://github.com/harfbuzz/harfbuzz/archive/refs/tags/6.0.0.tar.gz')
            ->withFile('harfbuzz-6.0.0.tar.gz')
            ->withLabel('library')
            ->withPrefix('/usr/harfbuzz/')
            //->withCleanBuildDirectory()
            ->withScriptBeforeConfigure('
            apk add python3 py3-pip 
            pip3 install meson  -i https://pypi.tuna.tsinghua.edu.cn/simple
            test -d /usr/harfbuzz/ && rm -rf /usr/harfbuzz/ 
            
            ')
            ->withConfigure(
                "
                ls -lh
                meson help
                meson setup --help

                meson setup  build \
                --backend=ninja \
                --prefix=/usr/harfbuzz \
                --default-library=static \
                -D freetype=disabled \
                -D tests=disabled \
                -D docs=disabled  \
                -D benchmark=disabled

                meson compile -C build
                # ninja -C builddir
                meson install -C build
                # ninja -C builddir install
                return 0
            "
            )
            ->withPkgConfig('/usr/harfbuzz/lib/pkgconfig')
            ->withPkgName('')
            ->withLdflags('-L/usr/harfbuzz/lib')
            ->depends('ninja')
            //->withSkipBuildInstall()
    );
}


function install_gettext(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('gettext'))
            ->withUrl('https://ftp.gnu.org/gnu/gettext/gettext-0.21.1.tar.gz')
            ->withHomePage('https://www.gnu.org/software/gettext/')
            ->withLicense('https://www.gnu.org/licenses/licenses.html', Library::LICENSE_GPL)
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/gettext')
            ->withScriptBeforeConfigure('
            test -d /usr/gettext && rm -rf /usr/gettext
            ')
            ->withConfigure('
            ./configure --help 
           
            ./configure --prefix=/usr/gettext enable_static=yes enable_shared=no \
             --disable-java \
             --without-git \
             --with-libiconv-prefix=/usr/libiconv \
             --with-libncurses-prefix=/usr/ncurses \
             --with-libxml2-prefix=/usr/libxml2/ \
             --with-libunistring-prefix \
             --with-libintl-prefix 
             
            ')
            ->withPkgName('gettext')
    );
}

function install_libidn2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libidn2'))
            ->withUrl('https://ftp.gnu.org/gnu/libidn/libidn2-2.3.4.tar.gz')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/libidn2')
            ->withScriptBeforeConfigure('
            test -d /usr/libidn2 && rm -rf /usr/libidn2
            
            apk add  gettext  coreutils
           
            ')
            ->withConfigure('
            ./configure --help 
            
            #  intl  依赖  gettext
            
            ./configure --prefix=/usr/libidn2 enable_static=yes enable_shared=no \
             --disable-doc \
             --with-libiconv-prefix=/usr/libiconv \
             --with-libintl-prefix
             
            ')
            ->withPkgName('libidn2')
    );
}

function install_jansson(Preprocessor $p)
{
    $jansson_prefix = JANSSON_PREFIX;
    $p->addLibrary(
        (new Library('jansson'))
            ->withHomePage('http://www.digip.org/jansson/')
            ->withUrl('https://github.com/akheron/jansson/archive/refs/tags/v2.14.tar.gz')
            ->withFile('jansson-v2.14.tar.gz')
            ->withManual('https://github.com/akheron/jansson.git')
            ->withLicense('https://github.com/akheron/jansson/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withPrefix($jansson_prefix)
            ->withCleanBuildDirectory()
            ->withCleanInstallDirectory($jansson_prefix)
            ->withConfigure(<<<EOF
             autoreconf -fi
            ./configure --help 
            ./configure \
            --prefix={$jansson_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
            )

            ->withPkgName('jansson')

    );

}

function install_nghttp2(Preprocessor $p)
{
    $nghttp2_prefix = NGHTTP2_PREFIX;
    $p->addLibrary(
        (new Library('nghttp2'))
            ->withHomePage('https://github.com/nghttp2/nghttp2.git')
            ->withUrl('https://github.com/nghttp2/nghttp2/releases/download/v1.51.0/nghttp2-1.51.0.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix($nghttp2_prefix)
            ->withCleanInstallDirectory($nghttp2_prefix)

            ->withConfigure(<<<EOF
            ./configure --help

            CPPFLAGS="$(pkg-config  --cflags-only-I  --static zlib libxml-2.0 jansson  libcares openssl )"  \
            LDFLAGS="$(pkg-config --libs-only-L      --static zlib libxml-2.0 jansson  libcares openssl )"  \
            LIBS="$(pkg-config --libs-only-l         --static zlib libxml-2.0 jansson  libcares openssl )"  \
            ./configure --prefix={$nghttp2_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-lib-only \
            --enable-python-bindings=no \
            --with-libxml2  \
            --with-jansson  \
            --with-zlib \
            --with-libcares
EOF
            )
            ->withLicense('https://github.com/nghttp2/nghttp2/blob/master/COPYING', Library::LICENSE_MIT)
            ->depends('openssl','zlib','libxml2','jansson','cares')
    );
}

function install_php_extension_micro(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('php_extension_micro'))
            ->withHomePage('https://github.com/dixyes/phpmicro')
            ->withUrl('https://github.com/dixyes/phpmicro/archive/refs/heads/master.zip')
            ->withFile('latest-phpmicro.zip')
            ->withLicense('https://github.com/dixyes/phpmicro/blob/master/LICENSE', Library::LICENSE_APACHE2)
            ->withManual('https://github.com/dixyes/phpmicro#readme')
            ->withLabel('php_extension')
            ->withCleanBuildDirectory()
            ->withUntarArchiveCommand('unzip')
            ->withScriptBeforeConfigure('return 0')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
            ->withSkipBuildInstall()
    );
}

function install_bison(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('bison', ))
            ->withHomePage('https://www.gnu.org/software/bison/')
            ->withUrl('http://ftp.gnu.org/gnu/bison/bison-3.8.tar.gz')
            ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
            ->withManual('https://www.gnu.org/software/bison/manual/')
            ->withLabel('build_env_bin')
            ->withCleanBuildDirectory()
            ->withConfigure("
             ./configure --help 
             ./configure --prefix=/usr/bison
            ")
            ->withBinPath('/usr/bison/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_re2c(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('re2c' ))
            ->withHomePage('http://re2c.org/')
            ->withUrl('https://github.com/skvadrik/re2c/releases/download/3.0/re2c-3.0.tar.xz')
            ->withLicense('https://github.com/skvadrik/re2c/blob/master/LICENSE', Library::LICENSE_GPL)
            ->withManual('https://re2c.org/build/build.html')
            ->withLabel('build_env_bin')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure('
             autoreconf -i -W all
            ')
            ->withConfigure("
             ./configure --help 
             ./configure --prefix=/usr/re2c
            ")
            ->withBinPath('/usr/re2c/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_php_internal_extension_curl_patch(Preprocessor $p)
{
    $workDir=$p->getWorkDir();
    $command = '';

    if(is_file("{$workDir}/ext/curl/config.m4.backup")){
        $originFileHash=md5(file_get_contents("{$workDir}/ext/curl/config.m4"));
        $backupFileHash=md5(file_get_contents("{$workDir}/ext/curl/config.m4.backup"));
        if($originFileHash == $backupFileHash){
            $command =<<<EOF
           test -f {$workDir}/ext/curl/config.m4.backup && rm -f {$workDir}/ext/curl/config.m4.backup
           test -f {$workDir}/ext/curl/config.m4.backup ||  sed -i.backup '75,82d' {$workDir}/ext/curl/config.m4
EOF;
        }
    } else {
        $command =<<<EOF
           test -f {$workDir}/ext/curl/config.m4.backup ||  sed -i.backup '75,82d' {$workDir}/ext/curl/config.m4
EOF;
    }

    $p->addLibrary(
    (new Library('patch_php_internal_extension_curl'))
        ->withHomePage('https://www.php.net/')
        ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Library::LICENSE_PHP)
        ->withUrl('https://github.com/php/php-src/archive/refs/tags/php-8.1.12.tar.gz')
        ->withManual('https://www.php.net/docs.php')
        ->withLabel('php_extension_patch')
        ->withScriptBeforeConfigure($command)
        ->withConfigure('return 0 ')
        ->disableDefaultPkgConfig()
        ->disableDefaultLdflags()
        ->disablePkgName()
    );
}

function install_ninja(Preprocessor $p)
{
    $p->addLibrary(
        $lib = (new Library('ninja'))
            ->withHomePage('https://ninja-build.org/')
            //->withUrl('https://github.com/ninja-build/ninja/releases/download/v1.11.1/ninja-linux.zip')
            ->withUrl('https://github.com/ninja-build/ninja/archive/refs/tags/v1.11.1.tar.gz')
            ->withFile('ninja-build-v1.11.1.tar.gz')
            ->withLicense('https://github.com/ninja-build/ninja/blob/master/COPYING', Library::LICENSE_APACHE2)
            ->withManual('https://ninja-build.org/manual.html')
            ->withManual('https://github.com/ninja-build/ninja/wiki')
            ->withLabel('build_env_bin')
            //->withCleanBuildDirectory()
            //->withUntarArchiveCommand('unzip')
            ->withConfigure(
                "
                /usr/bin/ar -h 
                cmake -Bbuild-cmake -D CMAKE_AR=/usr/bin/ar
                cmake --build build-cmake
                mkdir -p /usr/ninja/bin/
                cp build-cmake/ninja /usr/ninja/bin/
                return 0 ;
                ./configure.py --bootstrap
                mkdir -p /usr/ninja/bin/
                cp ninja /usr/ninja/bin/
                return 0 ;
            "
            )
            ->withBinPath('/usr/ninja/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );

    if ($p->getOsType() == 'macos') {
        $lib->withUrl('https://github.com/ninja-build/ninja/releases/download/v1.11.1/ninja-mac.zip');
    }


}

function install_nettle($p)
{
    $p->addLibrary(
        (new Library('nettle'))
            ->withHomePage('https://www.lysator.liu.se/~nisse/nettle/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://ftp.gnu.org/gnu/nettle/nettle-3.8.tar.gz')
            ->withFile('nettle-3.8.tar.gz')
            ->withPrefix('/usr/nettle/')
            ->withConfigure(
                '
             ./configure --help
            ./configure \
            --prefix=/usr/nettle \
            --enable-static \
            --disable-shared
            '
            )
            ->withPkgName('nettle')
    );
}

function install_libtasn1($p)
{
    $libtasn1_prefix = LIBTASN1_PREFIX;
    $p->addLibrary(
        (new Library('libtasn1'))
            ->withHomePage('https://www.gnu.org/software/libtasn1/')
            ->withLicense('https://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/libtasn1/manual/')
            ->withUrl('https://ftp.gnu.org/gnu/libtasn1/libtasn1-4.19.0.tar.gz')
            ->withPrefix($libtasn1_prefix)
            ->withConfigure(<<<EOF
            ./configure --help
            ./configure \
            --prefix={$libtasn1_prefix} \
            --enable-static=yes \
            --enable-shared=no
EOF
            )
            ->withPkgName('libtasn1')
    );

}
function install_libexpat($p)
{
    $p->addLibrary(
        (new Library('libexpat'))
            ->withHomePage('https://github.com/libexpat/libexpat')
            ->withLicense('https://github.com/libexpat/libexpat/blob/master/COPYING', Library::LICENSE_MIT)
            ->withManual('https://libexpat.github.io/doc/')
            ->withUrl('https://github.com/libexpat/libexpat/releases/download/R_2_5_0/expat-2.5.0.tar.gz')
            ->withPrefix('/usr/libexpat/')
            ->withCleanBuildDirectory()
            ->withConfigure(
                '
             ./configure --help
            
            ./configure \
            --prefix=/usr/libexpat/ \
            --enable-static=yes \
            --enable-shared=no
            '
            )
            ->withPkgName('expat')
    );
}

function install_unbound($p)
{
    $p->addLibrary(
        (new Library('unbound'))
            ->withHomePage('https://nlnetlabs.nl/unbound')
            ->withLicense('https://github.com/NLnetLabs/unbound/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withManual('https://unbound.docs.nlnetlabs.nl/en/latest/')
            ->withUrl('https://nlnetlabs.nl/downloads/unbound/unbound-1.17.1.tar.gz')
            ->withPrefix('/usr/unbound/')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure('
                test -d /usr/unbound/ && rm -rf /usr/unbound/
            ')
            ->withConfigure(
                '
             ./configure --help
            
            ./configure \
            --prefix= \
            --enable-static=yes \
            --enable-shared=no \
            --with-libsodium=/usr/libsodium \
            --with-libnghttp2=/usr/nghttp2 \
            --with-nettle=/usr/nettle \
            --with-ssl=/usr/openssl \
            --with-libexpat=/usr/libexpat/ \
            --with-dynlibmodule=no \
            --with-libunbound-only 
          
            '
            )
            ->withPkgName('unbound')
    );
}

function install_gnutls($p)
{
        $note=<<<EOF

        Required libraries:
            libnettle crypto back-end
            gmplib arithmetic library1
            
        Optional libraries:
        libtasn1 ASN.1 parsing - a copy is included in GnuTLS
        p11-kit for PKCS #11 support
        trousers for TPM support
        libidn2 for Internationalized Domain Names support
        libunbound for DNSSEC/DANE functionality
EOF;


    $p->addLibrary(
        (new Library('gnutls'))
            ->withHomePage('https://www.gnutls.org/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://www.gnupg.org/ftp/gcrypt/gnutls/v3.7/gnutls-3.7.8.tar.xz')
            ->withManual('https://gitlab.com/gnutls/gnutls.git')
            ->withManual('https://www.gnutls.org/download.html')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/gnutls')
            ->withConfigure(
                '
                 test -d /usr/gnutls && rm -rf /usr/gnutls
                 set -uex 
                export GMP_CFLAGS=$(pkg-config  --cflags --static gmp)
                export GMP_LIBS=$(pkg-config    --libs   --static gmp)
                export LIBTASN1_CFLAGS=$(pkg-config  --cflags --static libtasn1)
                export LIBTASN1_LIBS=$(pkg-config    --libs   --static libtasn1)
                
                export LIBIDN2_CFLAGS=$(pkg-config  --cflags --static libidn2)
                export LIBIDN2_LIBS=$(pkg-config    --libs   --static libidn2)
                
                
                export LIBBROTLIENC_CFLAGS=$(pkg-config  --cflags --static libbrotlienc)
                export LIBBROTLIENC_LIBS=$(pkg-config    --libs   --static libbrotlienc)
                
                export LIBBROTLIDEC_CFLAGS=$(pkg-config  --cflags --static libbrotlidec)
                export LIBBROTLIDEC_LIBS=$(pkg-config    --libs   --static libbrotlidec)

                export LIBZSTD_CFLAGS=$(pkg-config  --cflags --static libzstd)
                export LIBZSTD_LIBS=$(pkg-config    --libs   --static libzstd)
                
                export P11_KIT_CFLAGS=$(pkg-config  --cflags --static p11-kit-1)
                export P11_KIT_LIBS=$(pkg-config    --libs   --static p11-kit-1)
            
              
            
                export CPPFLAGS=$(pkg-config    --cflags   --static libbrotlicommon libbrotlienc libbrotlidec)
                export LIBS=$(pkg-config        --libs     --static libbrotlicommon libbrotlienc libbrotlidec)
                 //  exit 0 
                # ./bootstrap
                ./configure --help 
             
             
                ./configure \
                --prefix=/usr/gnutls \
                --enable-static=yes \
                --enable-shared=no \
                --with-zstd \
                --with-brotli \
                --with-libiconv-prefix=/usr/libiconv \
                --with-libz-prefix=/usr/zlib \
                --with-libintl-prefix \
                --with-included-unistring \
                --with-nettle-mini  \
                --with-included-libtasn1 \
                --without-tpm2 \
                --without-tpm \
                --disable-doc \
                --disable-tests 
               # --with-libev-prefix=/usr/libev \
              
            '
            )->withPkgName('gnutls')
    //依赖：nettle, hogweed, libtasn1, libidn2, p11-kit-1, zlib, libbrotlienc, libbrotlidec, libzstd -lgmp  -latomic
    );

}


function install_boringssl($p)
{
        $p->addLibrary(
            (new Library('boringssl'))
                ->withHomePage('https://boringssl.googlesource.com/boringssl/')
                ->withLicense('https://boringssl.googlesource.com/boringssl/+/refs/heads/master/LICENSE', Library::LICENSE_BSD)
                ->withUrl('https://github.com/google/boringssl/archive/refs/heads/master.zip')
                ->withFile('latest-boringssl.zip')
                ->withSkipDownload()
                ->withMirrorUrl('https://boringssl.googlesource.com/boringssl')
                ->withMirrorUrl('https://github.com/google/boringssl.git')
                ->withManual('https://boringssl.googlesource.com/boringssl/+/refs/heads/master/BUILDING.md')
                ->withUntarArchiveCommand('unzip')
                ->withCleanBuildDirectory()
                ->withPrefix('/usr/boringssl')
                ->withScriptBeforeConfigure('
                 test -d /usr/boringssl && rm -rf /usr/boringssl
                ')
                ->withConfigure(
                    '
                cd boringssl-master
                mkdir build
                cd build
                cmake -GNinja .. -DCMAKE_BUILD_TYPE=Release -DBUILD_SHARED_LIBS=0 -DCMAKE_INSTALL_PREFIX=/usr/boringssl
               
                cd ..
                # ninja
                ninja -C build
                
                ninja -C build install
            '
                )
                ->withSkipMakeAndMakeInstall()
                ->disableDefaultPkgConfig()
                //->withSkipBuildInstall()
        );

}

function install_wolfssl($p)
{
        $p->addLibrary(
            (new Library('wolfssl'))
                ->withHomePage('https://github.com/wolfSSL/wolfssl.git')
                ->withLicense('https://github.com/wolfSSL/wolfssl/blob/master/COPYING', Library::LICENSE_GPL)
                ->withUrl('https://github.com/wolfSSL/wolfssl/archive/refs/tags/v5.5.4-stable.tar.gz')
                ->withFile('wolfssl-v5.5.4-stable.tar.gz')
                ->withManual('https://wolfssl.com/wolfSSL/Docs.html')
                ->withCleanBuildDirectory()
                ->withPrefix('/usr/wolfssl')
                ->withScriptBeforeConfigure('
                 test -d /usr/wolfssl && rm -rf /usr/wolfssl
                ')

                ->withConfigure(<<<EOF
                ./autogen.sh
                ./configure --help
                
                ./configure  --prefix=/usr/wolfssl \
                --enable-static=yes \
                --enable-shared=no \
                --enable-all

EOF
                )
                //->withSkipMakeAndMakeInstall()
                ->withPkgName('wolfssl')
                //->withSkipBuildInstall()
        );

}

function install_nghttp3(Preprocessor $p)
{

    $p->addLibrary(
        (new Library('nghttp3'))
            ->withHomePage('https://github.com/ngtcp2/nghttp3')
            ->withManual('https://nghttp2.org/nghttp3/')
            ->withUrl('https://github.com/ngtcp2/nghttp3/archive/refs/tags/v0.8.0.tar.gz')
            //->withUrl('https://github.com/ngtcp2/nghttp3/archive/refs/heads/main.zip')
            ->withFile('nghttp3-v0.8.0.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/nghttp3')
            ->withConfigure('
                export GNUTLS_CFLAGS=$(pkg-config  --cflags --static gnutls)
                export GNUTLS_LIBS=$(pkg-config    --libs   --static gnutls)
           
            autoreconf -fi
            ./configure --help 
          
            ./configure --prefix=/usr/nghttp3 --enable-lib-only \
            --enable-shared=no \
            --enable-static=yes 
            
        ')
            ->withLicense('https://github.com/ngtcp2/nghttp3/blob/main/COPYING', Library::LICENSE_MIT)
            ->withPkgName('libnghttp3')
    );
}

function install_ngtcp2(Preprocessor $p)
{

    //libexpat pcre2 libidn2 brotli

    $p->addLibrary(
        (new Library('ngtcp2'))
            ->withHomePage('https://github.com/ngtcp2/ngtcp2')
            ->withManual('https://curl.se/docs/http3.html')
            ->withUrl('https://github.com/ngtcp2/ngtcp2/archive/refs/tags/v0.13.1.tar.gz')
            ->withFile('ngtcp2-v0.13.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/ngtcp2')
            ->withConfigure('

            # openssl does not have QUIC interface, disabling it
            # 
            # OPENSSL_CFLAGS=$(pkg-config  --cflags --static openssl)
            # OPENSSL_LIBS=$(pkg-config    --libs   --static openssl)
            
           
            export GNUTLS_CFLAGS=$(pkg-config  --cflags --static gnutls)
            export GNUTLS_LIBS=$(pkg-config    --libs   --static gnutls)
            export LIBNGHTTP3_CFLAGS=$(pkg-config  --cflags --static libnghttp3)
            export LIBNGHTTP3_LIBS=$(pkg-config    --libs   --static libnghttp3)
           
            export LIBEV_CFLAGS="-I/usr/libev/include"
            export LIBEV_LIBS="-L/usr/libev/lib -lev"
            
             autoreconf -fi
            ./configure --help 
          
            ./configure \
            --prefix=/usr/ngtcp2 \
            --enable-shared=no \
            --enable-static=yes \
            --with-gnutls=yes \
            --with-libnghttp3=yes \
            --with-libev=yes 
            ')
            ->withLicense('https://github.com/ngtcp2/ngtcp2/blob/main/COPYING', Library::LICENSE_MIT)
            ->withPkgName('libngtcp2  libngtcp2_crypto_gnutls')
    );
}
function install_quiche(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('quiche'))
            ->withHomePage('https://github.com/cloudflare/quiche')
            ->withManual('https://curl.se/docs/http3.html')
            ->withUrl('https://github.com/cloudflare/quiche/archive/refs/heads/master.zip')
            ->withFile('latest-quiche.zip')
            ->withCleanBuildDirectory()
            ->withUntarArchiveCommand('unzip')
            ->withPrefix('/usr/quiche')
            ->withScriptBeforeConfigure('
             test  -d /usr/quiche && rm -rf /usr/quiche
             # export RUSTUP_DIST_SERVER=https://mirrors.tuna.edu.cn/rustup
             # export RUSTUP_UPDATE_ROOT=https://mirrors.tuna.edu.cn/rustup/rustup
             export http_proxy=http://192.168.3.26:8015
             export https_proxy=http://192.168.3.26:8015
             source /root/.cargo/env
             cp -rf /work/pool/lib/boringssl /work/thirdparty/quiche/
             export OPENSSL_DIR=/usr/openssl
             export OPENSSL_STATIC=Yes
          
            ')
            ->withConfigure('
            cd quiche-master
            cargo build --help 
            
            export QUICHE_BSSL_PATH=/work/thirdparty/quiche/boringssl
            cargo build --package quiche --release --features ffi,pkg-config-meta,qlog
            mkdir -p quiche/deps/boringssl/src/lib
            ln -vnf $(find target/release -name libcrypto.a -o -name libssl.a) quiche/deps/boringssl/src/lib/
            exit 0 
        
            ')
            ->withLicense('https://github.com/cloudflare/quiche/blob/master/COPYING', Library::LICENSE_BSD)
            ->withPkgName('quiche')
    );
}
function install_msh3(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('msh3'))
            ->withHomePage('https://github.com/nibanks/msh3')
            ->withManual('https://github.com/nibanks/msh3.git')
            ->withUrl('https://github.com/nibanks/msh3/archive/refs/heads/main.zip')
            ->withFile('latest-msh3.zip')
            ->withFile('msh3')
            ->withSkipDownload()
            //->withCleanBuildDirectory()
            ->withUntarArchiveCommand('mv')
            ->withPrefix('/usr/msh3')
            ->withScriptBeforeConfigure('
              cp -rf /work/pool/lib/msh3 /work/thirdparty/msh3
              apk add bsd-compat-headers
            ')
            ->withConfigure(<<<EOF
            cd /work/thirdparty/msh3/msh3
            pwd
            ls -lh 
            mkdir build && cd build
            #  cmake -G 'Unix Makefiles' -DCMAKE_BUILD_TYPE=RelWithDebInfo .. -DCMAKE_INSTALL_PREFIX=/usr/
            cmake -G 'Unix Makefiles'  -DCMAKE_BUILD_TYPE=Release  .. -DBUILD_SHARED_LIBS=0 -DCMAKE_INSTALL_PREFIX=/usr/msh3 
            cmake --build .
            cmake --install .

EOF
            )
            ->withLicense('https://github.com/ngtcp2/ngtcp2/blob/main/COPYING', Library::LICENSE_MIT)
            ->withPkgName('msh3')
    );
}

function install_coreutils($p)
{
    /*
        glibc是一个核心C运行时库.它提供了像printf(3)和的东西fopen(3).

        glib 是一个用C编写的基于对象的事件循环和实用程序库.

        gnulib 是一个库,提供从POSIX API到本机API的适配器.

     */
    $p->addLibrary(
        (new Library('gnu_coreutils'))
            ->withHomePage('https://www.gnu.org/software/coreutils/')
            ->withLicense('https://www.gnu.org/licenses/gpl-2.0.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/coreutils/')
            ->withUrl('https://mirrors.aliyun.com/gnu/coreutils/coreutils-9.1.tar.gz')
            ->withFile('coreutils-9.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withConfigure(
                '
                ./bootstrap
                ./configure --help
                exit 0 
                  export FORCE_UNSAFE_CONFIGURE=1 
                  ./configure --prefix=/usr/gnu_coreutils \
                  --with-openssl=yes \
                  --with-libiconv-prefix=/usr/libiconv \
                  --with-libintl-prefix
             
            
            '
            )
            //->withSkipMakeAndMakeInstall()
            ->withPkgConfig('')
            ->withPkgName('')
    );
}

function install_gnulib($p)
{
    /*
        glibc是一个核心C运行时库.它提供了像printf(3)和的东西fopen(3).

        glib 是一个用C编写的基于对象的事件循环和实用程序库.

        gnulib 是一个库,提供从POSIX API到本机API的适配器.

     */
    $p->addLibrary(
        (new Library('gnulib'))
            ->withHomePage('https://www.gnu.org/software/gnulib/')
            ->withLicense('https://www.gnu.org/licenses/gpl-2.0.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/gnulib/manual/')
            ->withUrl('https://github.com/coreutils/gnulib/archive/refs/heads/master.zip')
            ->withFile('latest-gnulib.zip')
            ->withCleanBuildDirectory()
            ->withUntarArchiveCommand('unzip')
            ->withConfigure(
                '
               cd gnulib-master
             ./gnulib-tool --help
             return 0 ;
            '
            )
            ->withSkipMakeAndMakeInstall()
            ->withPkgConfig('')
            ->withPkgName('')
    );
}

function install_libunistring($p)
{
    $p->addLibrary(
        (new Library('libunistring'))
            ->withHomePage('https://www.gnu.org/software/libunistring/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://ftp.gnu.org/gnu/libunistring/libunistring-0.9.1.1.tar.gz')
            ->withFile('libunistring-0.9.1.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure('
            
            apk add coreutils
            
            test -d /usr/libunistring && rm -rf /usr/libunistring
            ')
            ->withConfigure(
                '
             ./configure --help
            
            ./configure \
            --prefix=/usr/libunistring \
            --enable-static \
            --disable-shared \
             --with-libiconv-prefix=/usr/libiconv 
            '
            )
            ->withPkgConfig('/usr/libunistring/lib/pkgconfig')
            ->withPkgName('libunistringe')
    );
}

function install_libevent($p)
{


    $p->addLibrary(
        (new Library('libevent'))
            ->withHomePage('https://github.com/libevent/libevent')
            ->withLicense('https://github.com/libevent/libevent/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/libevent/libevent/releases/download/release-2.1.12-stable/libevent-2.1.12-stable.tar.gz')
            ->withManual('https://libevent.org/libevent-book/')
            ->withPrefix('/usr/libevent')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            # 查看更多选项
            # cmake -LAH .
        mkdir build && cd build
        cmake ..   \
        -DCMAKE_INSTALL_PREFIX=/usr/libevent \
        -DEVENT__DISABLE_DEBUG_MODE=ON \
        -DCMAKE_BUILD_TYPE=Release \
        -DEVENT__LIBRARY_TYPE=STATIC  
  
EOF

            )
            ->withPkgName('libevent')
    );
}

function install_libuv($p)
{
    //as epoll/kqueue/event ports/inotify/eventfd/signalfd support
    $p->addLibrary(
        (new Library('libev'))
            ->withHomePage('http://software.schmorp.de/pkg/libev.html')
            ->withLicense('https://github.com/libevent/libevent/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('http://dist.schmorp.de/libev/libev-4.33.tar.gz')
            ->withManual('http://cvs.schmorp.de/libev/README')
            ->withPrefix('/usr/libev')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            ls -lh 
            ./configure --help 
            ./configure --prefix=/usr/libev \
            --enable-shared=no \
            --enable-static=yes
           
EOF

            )
            ->withPkgName('libev')
    //->withSkipBuildInstall()
    );

}

function install_libev($p)
{
    $p->addLibrary(
        (new Library('libev'))
            ->withHomePage('http://software.schmorp.de/pkg/libev.html')
            ->withLicense('https://github.com/libevent/libevent/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('http://dist.schmorp.de/libev/libev-4.33.tar.gz')
            ->withManual('http://cvs.schmorp.de/libev/README')
            ->withPrefix('/usr/libev')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            ls -lh 
            ./configure --help 
            ./configure --prefix=/usr/libev \
            --enable-shared=no \
            --enable-static=yes
           
EOF

            )
            ->withPkgName('libev')

    );
}

    function install_libunwind($p)
    {
        $p->addLibrary(
            (new Library('libunwind'))
                ->withHomePage('https://github.com/libunwind/libunwind.git')
                ->withLicense('https://github.com/libunwind/libunwind/blob/master/LICENSE', Library::LICENSE_MIT)
                ->withUrl('https://github.com/libunwind/libunwind/releases/download/v1.6.2/libunwind-1.6.2.tar.gz')
                ->withFile('libunwind-1.6.2.tar.gz')
                ->withPrefix('/usr/libunwind')
                ->withConfigure(
                    '
                 autoreconf -i
                 
                ./configure --help ;
                ./configure \
                --prefix=/usr/libunwind \
                --enable-static=yes \
                --enable-shared=no
                '
                )
                ->withPkgName('libunwind-coredump  libunwind-generic   libunwind-ptrace    libunwind-setjmp    libunwind')
                ->withSkipBuildInstall()
        );
    }
    function install_socat($p)
    {
        // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
        $p->addLibrary(
            (new Library('socat'))
                ->withHomePage('http://www.dest-unreach.org/socat/')
                ->withLicense('http://www.dest-unreach.org/socat/doc/README', Library::LICENSE_GPL)
                ->withUrl('http://www.dest-unreach.org/socat/download/socat-1.7.4.4.tar.gz')
                ->withConfigure(
                    '
            pkg-config --cflags --static readline
            pkg-config  --libs --static readline
            ./configure --help ;
            CFLAGS=$(pkg-config --cflags --static  libcrypto  libssl    openssl readline)
            export CFLAGS="-static -O2 -Wall -fPIC $CFLAGS "
            export LDFLAGS=$(pkg-config --libs --static libcrypto  libssl    openssl readline)
            # LIBS="-static -Wall -O2 -fPIC  -lcrypt  -lssl   -lreadline"
            # CFLAGS="-static -Wall -O2 -fPIC"
            ./configure \
            --prefix=/usr/socat \
            --enable-readline \
            --enable-openssl-base=/usr/openssl
            ')
                ->withSkipBuildInstall()
        );
    }

    function install_jemalloc($p)
    {
        // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
        $p->addLibrary(
            (new Library('jemalloc'))
                ->withHomePage('http://jemalloc.net/')
                ->withLicense(
                    'https://github.com/jemalloc/jemalloc/blob/dev/COPYING',
                    Library::LICENSE_GPL
                )
                ->withUrl('https://github.com/jemalloc/jemalloc/archive/refs/tags/5.3.0.tar.gz')
                ->withFile('jemalloc-5.3.0.tar.gz')
                ->withConfigure(
                    '
            sh autogen.sh
            ./configure --help ;
            ./configure \
            --prefix=/usr/jemalloc \
            --enable-static=yes \
            --enable-shared=no \
            --with-static-libunwind=/usr/libunwind/lib/libunwind.a
            '
                )
                ->withPkgConfig('/usr/jemalloc/lib/pkgconfig')
                ->withPkgName('jemalloc')
                ->withLdflags('/usr/jemalloc/lib')
                ->withSkipBuildInstall()
        );
    }

    function install_tcmalloc($p)
    {
        // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
        $p->addLibrary(
            (new Library('tcmalloc'))
                ->withHomePage('https://google.github.io/tcmalloc/overview.html')
                ->withLicense('https://github.com/google/tcmalloc/blob/master/LICENSE', Library::LICENSE_APACHE2)
                ->withUrl('https://github.com/google/tcmalloc/archive/refs/heads/master.zip')
                ->withFile('tcmalloc.zip')
                ->withUntarArchiveCommand('unzip')
                ->withCleanBuildDirectory()
                ->withConfigure(
                    '
            cd  tcmalloc-master/
            bazel help
            bazel build
            return
            ./configure \
            --prefix=/usr/tcmalloc \
            --enable-static \
            --disable-shared
            '
                )
                ->withPkgConfig('/usr/tcmalloc/lib/pkgconfig')
                ->withPkgName('tcmalloc')
                ->withLdflags('/usr/tcmalloc/lib')
                ->withSkipBuildInstall()
        );
    }

function install_aria2($p)
{
        // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
        $p->addLibrary(
            (new Library('aria2c'))
                ->withHomePage('https://aria2.github.io/')
                ->withLicense('https://github.com/aria2/aria2/blob/master/COPYING', Library::LICENSE_GPL)
                ->withUrl('https://github.com/aria2/aria2/releases/download/release-1.36.0/aria2-1.36.0.tar.gz')
                ->withManual('https://aria2.github.io/manual/en/html/README.html')
                ->withCleanBuildDirectory()
                ->withConfigure(
                    '
            # CFLAGS=$(pkg-config --cflags --static  libcrypto  libssl    openssl readline)
            # export CFLAGS="-static -O2 -Wall -fPIC $CFLAGS "
            # export LDFLAGS=$(pkg-config --libs --static libcrypto  libssl    openssl readline)
            # LIBS="-static -Wall -O2 -fPIC  -lcrypt  -lssl   -lreadline"
            # CFLAGS="-static -Wall -O2 -fPIC"
            export ZLIB_CFLAGS=$(pkg-config --cflags --static zlib) ;
            export  ZLIB_LIBS=$(pkg-config --libs --static zlib) ;
            ./configure --help ;
             ARIA2_STATIC=yes
            ./configure \
            --with-ca-bundle="/etc/ssl/certs/ca-certificates.crt" \
            --prefix=/usr/aria2 \
            --enable-static=yes \
            --enable-shared=no \
            --enable-libaria2 \
            --with-libuv \
            --without-gnutls \
            --with-openssl \
            --with-libiconv-prefix=/usr/libiconv/ \
            --with-libz
            # --with-tcmalloc
            '
                )
                ->withSkipBuildInstall()
        );
    }

function install_bazel(Preprocessor $p)
{
        $p->addLibrary(
            (new Library('bazel'))
                ->withHomePage('https://bazel.build')
                ->withLicense('https://github.com/bazelbuild/bazel/blob/master/LICENSE', Library::LICENSE_APACHE2)
                ->withUrl('https://github.com/bazelbuild/bazel/releases/download/6.0.0/bazel-6.0.0-linux-x86_64')
                ->withManual('/usr/bazel/bin/')
                ->withManual('https://bazel.build/install')
                ->withCleanBuildDirectory()
                ->withUntarArchiveCommand('mv')
                ->withScriptBeforeConfigure(
                    '
                test -d /usr/bazel/bin/ || mkdir -p /usr/bazel/bin/
                mv bazel /usr/bazel/bin/
                chmod a+x /usr/bazel/bin/bazel
                return 0 
               '
                )
                ->disableDefaultPkgConfig()
                ->disablePkgName()
                ->disableDefaultLdflags()
                ->withSkipBuildInstall()
        );

}

 function install_libelf(Preprocessor $p)
{

    $p->addLibrary(
        (new Library('libelf'))
            ->withHomePage('https://github.com/WolfgangSt/libelf.git')
            ->withLicense('https://github.com/WolfgangSt/libelf/blob/master/COPYING.LIB', Library::LICENSE_GPL)
            ->withUrl('https://github.com/libbpf/libbpf/archive/refs/tags/v1.1.0.tar.gz')
            ->withFile('libbpf-v1.1.0.tar.gz')
            ->withManual('https://github.com/WolfgangSt/libelf.git')
            ->withPrefix('/usr/libelf')
            ->withCleanBuildDirectory()
            ->withConfigure(
<<<EOF
                pwd
                test -d {$p->getBuildDir()}/libelf && rm -rf {$p->getBuildDir()}/libelf
                cp -rf {$p->getWorkDir()}/pool/lib/libelf {$p->getBuildDir()}/
                cd {$p->getBuildDir()}/libelf
                ./configure --help 
                ./configure --prefix=/usr/libelf \
                --enable-compat \
                --enable-shared=no 
  
EOF
             )
            ->withMakeInstallCommand('install-local')
            ->withPkgName('libelf')
    );
}

function install_libbpf(Preprocessor $p)
{


    $p->addLibrary(
    (new Library('libbpf'))
        ->withHomePage('https://github.com/libbpf/libbpf.git')
        ->withLicense('https://github.com/libbpf/libbpf/blob/master/LICENSE.BSD-2-Clause', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/libbpf/libbpf/archive/refs/tags/v1.1.0.tar.gz')
        ->withFile('libbpf-v1.1.0.tar.gz')
        ->withManual('https://libbpf.readthedocs.io/en/latest/api.html')
        ->withPrefix('/usr/libbpf')
        ->withCleanBuildDirectory()
        ->withConfigure(
            <<<EOF
                cd src
                BUILD_STATIC_ONLY=y  make 
                exit 0 
                mkdir build /usr/libbpf
                BUILD_STATIC_ONLY=y OBJDIR=build DESTDIR=/usr/libbpf make install
                eixt 0 
    
EOF
           )
        ->withPkgName('libbpf')
    );
}

function install_valgrind(Preprocessor $p)
{

    $p->addLibrary(
        (new Library('valgrind'))
            ->withHomePage('https://valgrind.org/')
            ->withLicense('https://github.com/libbpf/libbpf/blob/master/LICENSE.BSD-2-Clause', Library::LICENSE_LGPL)
            ->withUrl('https://sourceware.org/pub/valgrind/valgrind-3.20.0.tar.bz2')
            ->withManual('https://valgrind.org/docs/man')
            ->withPrefix('/usr/valgrind')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF

./autogen.sh
./configure --prefix=/usr/valgrind

  
EOF

            )
            ->withPkgName('valgrind')
            ->withBinPath('/usr/valgrind/bin/')
    );
}
function install_snappy(Preprocessor $p)
{

    $p->addLibrary(
        (new Library('valgrind'))
            ->withHomePage('https://github.com/google/snappy')
            ->withLicense('https://github.com/google/snappy/blob/main/COPYING', Library::LICENSE_BSD)
            ->withUrl('https://github.com/google/snappy/archive/refs/tags/1.1.9.tar.gz')
            ->withFile('snappy-1.1.9.tar.gz')
            ->withManual('https://github.com/google/snappy/blob/main/README.md')
            ->withPrefix('/usr/snappy')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF

git submodule update --init
mkdir build
cd build && cmake ../ && make

  
EOF

            )
            ->withPkgName('snappy')
            ->withBinPath('/usr/snappy/bin/')
    );
}
function install_kerberos(Preprocessor $p)
{

    $p->addLibrary(
        (new Library('kerberos'))
            ->withHomePage('https://web.mit.edu/kerberos/')
            ->withLicense('https://github.com/google/snappy/blob/main/COPYING', Library::LICENSE_BSD)
            ->withUrl('https://kerberos.org/dist/krb5/1.20/krb5-1.20.1.tar.gz')
            ->withFile('krb5-1.20.1.tar.gz')
            ->withManual('https://web.mit.edu/kerberos/krb5-1.20/README-1.20.1.txt')
            //源码包： doc/html/admin/install.html
            ->withPrefix('/usr/kerberos')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
pwd
exit 0 
  
EOF

            )
            ->withPkgName('kerberos')
            ->withBinPath('/usr/kerberos/bin/')
    );
}function install_fontconfig(Preprocessor $p)
{

    $p->addLibrary(
        (new Library('fontconfig'))
            ->withHomePage('https://www.freedesktop.org/wiki/Software/fontconfig/')
            ->withLicense('https://www.freedesktop.org/software/fontconfig/webfonts/Licen.TXT', Library::LICENSE_SPEC)
            //->withUrl('https://gitlab.freedesktop.org/fontconfig/fontconfig/-/archive/main/fontconfig-main.tar.gz')
            ->withUrl('https://gitlab.freedesktop.org/fontconfig/fontconfig/-/tags/2.14.2')
            //download font https://www.freedesktop.org/software/fontconfig/webfonts/webfonts.tar.gz
            ->withFile('fontconfig-2.14.2.tar.gz')
            ->withManual('https://gitlab.freedesktop.org/fontconfig/fontconfig')
            ->withPrefix('/usr/fontconfig')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
pwd
exit 0 
  
EOF

            )
            ->withPkgName('fontconfig')
            ->withBinPath('/usr/fontconfig/bin/')
    );
}




function install_p11_kit(Preprocessor $p)
{


    $p->addLibrary(
        (new Library('p11_kit'))
            ->withHomePage('https://github.com/p11-glue/p11-kit.git')
            ->withLicense('https://github.com/p11-glue/p11-kit/blob/master/COPYING', Library::LICENSE_BSD)
            ->withManual('https://p11-glue.github.io/p11-glue/p11-kit.html')
            ->withManual('https://p11-glue.github.io/p11-glue/p11-kit/manual/devel-building.html')
            ->withUrl('https://github.com/p11-glue/p11-kit/archive/refs/tags/0.24.1.tar.gz')
            //构建选项参参考文档： https://mesonbuild.com/Builtin-options.html
            ->withFile('p11-kit-0.24.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/p11_kit/')
            ->withConfigure(
                '
          
                # apk add python3 py3-pip  gettext  coreutils
                # pip3 install meson  -i https://pypi.tuna.tsinghua.edu.cn/simple
            
            echo $PATH;
            #./autogen.sh
            #./configure --help
            # --with-libtasn1 --with-libffi
           
            # meson setup -Dprefix=/usr/p11_kit/ -Dsystemd=disabled -Dbash_completion=disabled  --reconfigure  _build
            # run "ninja reconfigure" or "meson setup --reconfigure"
            # ninja reconfigure -C _build
            # meson setup --reconfigure _build
            meson setup  -Dprefix=/usr/p11_kit/ -Dsystemd=disabled    -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Dprefer_static=true \
            -Ddebug=false \
            -Dunity=off \
             _build
             
           
            # meson setup --wipe
            
            meson compile -C _build
            
           # DESTDIR=/usr/p11_kit/  meson install -C _build
            meson install -C _build
            exit 0 
            '
            )
            ->withBypassMakeAndMakeInstall()
            ->withPkgName('p11_kit')
    );
}