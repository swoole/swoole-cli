<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;
// ================================================================================================
// Library
// ================================================================================================

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
            ->withConfigure('BZIP2_CFLAGS="-I' . BZIP2_PREFIX . '/include" & \\' .
                'BZIP2_LIBS="-L' . BZIP2_PREFIX . '/lib -lbz2" & \\' .
                'PATH="' . PNG_PREFIX . '/bin:$PATH" & \\' .
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
    $p->addLibrary(
        (new Library('zip'))
            ->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
            ->withPrefix(ZIP_PREFIX)
            ->withCleanBuildDirectory()
            ->withConfigure('
                 cmake -Wno-dev .  \
                -DCMAKE_INSTALL_PREFIX=' . ZIP_PREFIX . ' \
                -DBUILD_TOOLS=OFF \
                -DBUILD_EXAMPLES=OFF \
                -DBUILD_DOC=OFF \
                -DLIBZIP_DO_INSTALL=ON \
                -DBUILD_SHARED_LIBS=OFF \
                -DENABLE_GNUTLS=OFF  \
                -DENABLE_MBEDTLS=OFF \
                -DENABLE_OPENSSL=ON \
                -DOPENSSL_USE_STATIC_LIBS=TRUE \
                -DOPENSSL_LIBRARIES=' . OPENSSL_PREFIX . '/lib \
                -DOPENSSL_INCLUDE_DIR=' . OPENSSL_PREFIX . '/include \
                -DZLIB_LIBRARY=' . ZLIB_PREFIX . '/lib \
                -DZLIB_INCLUDE_DIR=' . ZLIB_PREFIX . '/include \
                -DENABLE_BZIP2=ON \
                -DBZIP2_LIBRARIES=' . BZIP2_PREFIX . '/lib \
                -DBZIP2_LIBRARY=' . BZIP2_PREFIX . '/lib \
                -DBZIP2_INCLUDE_DIR=' . BZIP2_PREFIX . '/include \
                -DBZIP2_NEED_PREFIX=ON \
                -DENABLE_LZMA=OFF  \
                -DENABLE_ZSTD=OFF
            ')
            ->withMakeOptions('VERBOSE=1')
            #->withMakeOptions('VERBOSE=1 all  ') //VERBOSE=1
            #->withMakeInstallOptions("VERBOSE=1  PREFIX=/usr/zip")
            ->withPkgName('libzip')
            ->withHomePage('https://libzip.org/')
            ->withLicense('https://libzip.org/license/', Library::LICENSE_BSD)
            ->depends('openssl', 'zlib', 'bzip2')
                /*
                -DENABLE_LZMA=OFF  \
                -DLIBLZMA_LIBRARY=/usr/liblzma/lib \
                -DLIBLZMA_INCLUDE_DIR=/usr/liblzma/include \
                -DLIBLZMA_HAS_AUTO_DECODER=ON  \
                -DLIBLZMA_HAS_EASY_ENCODER=ON  \
                -DLIBLZMA_HAS_LZMA_PRESET=ON \
                -DENABLE_ZSTD=OFF \
                -DZstd_LIBRARY=/usr/libzstd/lib \
                -DZstd_INCLUDE_DIR=/usr/libzstd/include
            */
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
    $p->addLibrary(
        (new Library('brotli'))
            ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
            ->withFile('brotli-1.0.9.tar.gz')
            ->withPrefix(BROTLI_PREFIX)
            ->withConfigure('cmake -DCMAKE_BUILD_TYPE=Release -DBUILD_SHARED_LIBS=OFF -DCMAKE_INSTALL_PREFIX=' . BROTLI_PREFIX . ' .')
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
            ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/google/brotli')
    );
}

function install_curl(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('curl'))
            ->withUrl('https://curl.se/download/curl-7.88.0.tar.gz')
            ->withPrefix(CURL_PREFIX)
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(<<<EOF
              test -d /usr/curl && rm -rf /usr/curl 
EOF
            )
            ->withConfigure(
                <<<EOF
                autoreconf -fi 
               ./configure --help 
               ./configure --help | grep '--with-'
               ./configure --help | grep '=PATH'
               
               # TLS 
               # https://stackoverflow.com/questions/67204980/wolfssl-vs-mbedtls-vs-openssl-what-is-the-difference
               # OpenSSL GnuTLS mbedTLS WolfSSL BearSSL rustls NSS,
               
               # https://curl.se/docs/http3.html 
               
EOF

                .
                'autoreconf -fi && ./configure --prefix=' . CURL_PREFIX .
                ' --enable-static --disable-shared --with-openssl=' . OPENSSL_PREFIX . ' \\' .PHP_EOL . <<<EOF
                --without-librtmp \
                --disable-ldap \
                --disable-rtsp \
                --with-zlib=/usr/zlib \
                --with-zstd=/usr/libzstd \
                --with-libidn2=/usr/libidn2 \
                --with-nghttp2=/usr/nghttp2
             
                
:<<_EOF_
                    
                --with-brotli=/usr/brotli \
                --with-nghttp3=PATH  
                --with-quiche=PATH      
                --with-msh3=PATH      
_EOF_
EOF

            )
            ->withPkgName('libcurl')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withHomePage('https://curl.se/')
            ->depends('openssl', 'cares', 'zlib')
    );
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
    $p->addLibrary(
        (new Library('liblz4'))
            ->withHomePage('https://github.com/lz4/lz4.git')
            ->withLicense('https://github.com/lz4/lz4/blob/dev/LICENSE', Library::LICENSE_GPL)
            ->withUrl('https://github.com/lz4/lz4/archive/refs/tags/v1.9.4.tar.gz')
            ->withFile('lz4-v1.9.4.tar.gz')
            ->withPkgName('liblz4')
            ->withPrefix('/usr/liblz4')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure("
            test -d /usr/liblz4/ && rm -rf /usr/liblz4/ 
            ")
            ->withConfigure(<<<EOF
            cd build/cmake/
           cmake . -DCMAKE_INSTALL_PREFIX=/usr/liblz4/ 
EOF
            )
            //可以使用CMAKE 编译 也可以不使用
            //不使用CMAKE，需要自己修改安装目录
            //->withMakeOptions('INSTALL_PROGRAM=/usr/liblz4/')
            //->withMakeInstallOptions("DESTDIR=/usr/liblz4/")
    );
}

function install_liblzma(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('liblzma'))
            ->withHomePage('https://tukaani.org/xz/')
            ->withLicense('https://github.com/tukaani-project/xz/blob/master/COPYING.GPLv3', Library::LICENSE_LGPL)
            ->withManual('https://github.com/tukaani-project/xz.git')
            //->withUrl('https://tukaani.org/xz/xz-5.2.9.tar.gz')
            //->withFile('xz-5.2.9.tar.gz')
            ->withUrl('https://github.com/tukaani-project/xz/releases/download/v5.4.1/xz-5.4.1.tar.gz')
            ->withFile('xz-5.4.1.tar.gz')
            ->withPrefix('/usr/liblzma/')
            ->withConfigure('./configure --prefix=/usr/liblzma/ --enable-static  --disable-shared --disable-doc')
            ->withPkgName('liblzma')

    );
}

function install_libzstd(Preprocessor $p)
{
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
            ')
            ->withConfigure('
            ./configure --help 
           
            ./configure --prefix=/usr/libidn2 enable_static=yes enable_shared=no \
             --disable-doc \
             --with-libiconv-prefix=/usr/libiconv \
             --with-libintl-prefix
             # --with-libunistring-prefix
             # --with-libintl-prefix
             # intl  依赖 gettext
            ')
            ->withPkgName('libidn2')
    );
}

function install_nghttp2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('nghttp2'))
            ->withHomePage('https://github.com/nghttp2/nghttp2.git')
            ->withUrl('https://github.com/nghttp2/nghttp2/releases/download/v1.51.0/nghttp2-1.51.0.tar.gz')
            ->withCleanBuildDirectory()
            ->withPrefix('/usr/nghttp2')
            ->withScriptBeforeConfigure('
             test -d /usr/nghttp2 && rm -rf /usr/nghttp2
            ')
            ->withConfigure('./configure --prefix=/usr/nghttp2 enable_static=yes enable_shared=no')
            ->withLicense('https://github.com/nghttp2/nghttp2/blob/master/COPYING', Library::LICENSE_MIT)
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
    // https://github.com/aledbf/socat-static-binary/blob/master/build.sh
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
             return  0
            ./configure \
            --prefix=/usr/nettle \
            --enable-static \
            --disable-shared
            '
            )
            ->withPkgName('nettle')
    );
}

function install_gnu_tls($p)
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
            (new Library('gnu_tls'))
                ->withHomePage('https://www.gnutls.org/')
                ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
                ->withUrl('https://www.gnupg.org/ftp/gcrypt/gnutls/v3.7/gnutls-3.7.8.tar.xz')
                ->withManual('https://gitlab.com/gnutls/gnutls.git')
                ->withConfigure(
                    '
            ./configure --help ;
            ./configure \
            --prefix=/usr/gnutls \
             --enable-static \
            --disable-shared \
            --without-zstd \
            --without-tpm2 \
            --without-tpm \
            --disable-doc \
            --disable-tests \
            --without-included-unistring
            '
                )
                //->withPkgConfig('/usr/gnutls/lib/pkgconfig')
                ->disableDefaultPkgConfig()
                //->withPkgName('hogweed nettle')
                ->disablePkgName()
                //->withLdflags('/usr/gnutls/lib')
                ->disableDefaultLdflags()
                ->withSkipBuildInstall()
        );

}

function install_nghttp3(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('nghttp3'))
            ->withHomePage('https://github.com/ngtcp2/nghttp3')
            ->withUrl('https://github.com/ngtcp2/nghttp3/archive/refs/heads/main.zip')
            ->withFile('latest-nghttp3.zip')
            ->withPrefix('/usr/nghttp3')
            ->withConfigure('./configure --prefix=/usr/nghttp3 enable_static=yes enable_shared=no')
            ->withLicense('https://github.com/ngtcp2/nghttp3/blob/main/COPYING', Library::LICENSE_MIT)
    );
}function install_ngtcp2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('ngtcp2'))
            ->withHomePage('https://github.com/ngtcp2/ngtcp2')
            ->withUrl('https://github.com/ngtcp2/ngtcp2/archive/refs/heads/main.zip')
            ->withFile('latest-ngtcp2.zip')
            ->withPrefix('/usr/nghttp2')
            ->withConfigure('./configure --prefix=/usr/ngtcp2 enable_static=yes enable_shared=no')
            ->withLicense('https://github-com.proxy.zibenyulun.cn/ngtcp2/ngtcp2/blob/main/COPYING', Library::LICENSE_MIT)
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
            ->disableDefaultPkgConfig()
            ->disablePkgName()
            ->disableDefaultLdflags()
            ->withSkipBuildInstall()
    );
}
