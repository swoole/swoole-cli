<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;
// ================================================================================================
// Library
// ================================================================================================

function install_openssl(Preprocessor $p)
{
    $p->addLibrary((new Library('openssl', '/usr/openssl'))
        ->withUrl('https://www.openssl.org/source/openssl-1.1.1p.tar.gz')
        ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
        ->withHomePage('https://www.openssl.org/')
        ->withConfigure('./config' . ($p->getOsType() === 'macos' ? '' : ' -static --static') . ' no-shared --prefix=/usr/openssl')
        ->withMakeInstallOptions('install_sw')
        ->withPkgConfig('/usr/openssl/lib/pkgconfig')
        ->withPkgName('libcrypto libssl openssl')
        ->withLdflags('-L/usr/openssl/lib')

    );
}

function install_libiconv(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libiconv', '/usr/libiconv'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withConfigure('./configure --prefix=/usr/libiconv enable_static=yes enable_shared=no')
            ->disableDefaultPkgConfig()
    );
}

// MUST be in the /usr directory
// Dependent libiconv
function install_libxml2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libxml2'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxml2/-/archive/v2.9.10/libxml2-v2.9.10.tar.gz')
            ->withLicense('http://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->withConfigure('
            ./autogen.sh && ./configure \
             --prefix=/usr/libxml2/ \
             --with-iconv=/usr/libiconv \
             --enable-static=yes \
             --enable-shared=no \
             --without-python \
            ')
            ->withPkgName('libxml-2.0')
            ->withPkgConfig('/usr/libxml2/lib/pkgconfig')
            ->withLdflags('-L/usr/libxml2/lib')

    );
}

// MUST be in the /usr directory
// Dependent libxml2
function install_libxslt(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libxslt'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxslt/-/archive/v1.1.34/libxslt-v1.1.34.tar.gz')
            ->withLicense('http://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr/libxslt/ --enable-static=yes --enable-shared=no')
            ->withPkgConfig('/usr/libxslt/lib/pkgconfig')
            ->withPkgName('libexslt libxslt')
            ->withLdflags('-L/usr/libxslt/lib')
    );
}

function install_imagemagick(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('imagemagick', '/usr/imagemagick'))
            ->withUrl('https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.0-19.tar.gz')
            ->withFile('imagemagick-7.1.0-19.tar.gz')
            ->withLicense('https://imagemagick.org/script/license.php', Library::LICENSE_APACHE2)
            ->withConfigure('
              ./configure \
              --prefix=/usr/imagemagick \
              --enable-static\
              --disable-shared \
              --with-zip=no \
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
              --with-freetype=no
              ')
            ->withPkgName('ImageMagick')
    );
}

function install_gmp(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('gmp', '/usr/gmp'))
            ->withUrl('https://gmplib.org/download/gmp/gmp-6.2.1.tar.lz')
            ->withHomePage('https://gmplib.org/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withConfigure('./configure --prefix=/usr/gmp --enable-static --disable-shared')
            ->withPkgConfig('/usr/gmp/lib/pkgconfig')
            ->withPkgName('gmp')
            ->withLdflags('-L/usr/gmp/lib')
    );
}

function install_giflib(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('giflib'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/giflib/giflib-5.2.1.tar.gz')
            ->withLicense('http://giflib.sourceforge.net/intro.html', Library::LICENSE_SPEC)
            ->withCleanBuildDirectory()
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
            # ->withMakeInstallOptions('install-include DESTDIR=/usr/giflib && make  install-lib-static DESTDIR=/usr/giflib')
            ->withLdflags('-L/usr/giflib/lib')
            ->disableDefaultPkgConfig()
    );
}

function install_libpng(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libpng', '/usr/libpng'))
            ->withUrl('https://nchc.dl.sourceforge.net/project/libpng/libpng16/1.6.37/libpng-1.6.37.tar.gz')
            ->withLicense('http://www.libpng.org/pub/png/src/libpng-LICENSE.txt', Library::LICENSE_SPEC)
            ->withConfigure('./configure --prefix=/usr/libpng --enable-static --disable-shared')
            ->withPkgName('libpng libpng16')
    );
}

function install_libjpeg(Preprocessor $p)
{
    $lib = new Library('libjpeg','/usr/libjpeg/');
    $lib->withUrl('https://codeload.github.com/libjpeg-turbo/libjpeg-turbo/tar.gz/refs/tags/2.1.2')
        ->withFile('libjpeg-turbo-2.1.2.tar.gz')
        ->withHomePage('https://libjpeg-turbo.org/')
        ->withLicense('https://github.com/libjpeg-turbo/libjpeg-turbo/blob/main/LICENSE.md', Library::LICENSE_BSD)
        ->withConfigure('cmake -G"Unix Makefiles" -DCMAKE_INSTALL_PREFIX=/usr/libjpeg .')
        ->withLdflags('-L/usr/libjpeg/lib64')
        ->withPkgConfig('/usr/libjpeg/lib64/pkgconfig')
        ->withPkgName('libjpeg libturbojpeg');

    if ($p->getOsType() === 'macos') {
        $lib->withLdflags('-L/usr/libjpeg/lib')
            ->withPkgConfig('/usr/libjpeg/lib/pkgconfig')
            ->withScriptAfterInstall('find ' . $lib->prefix . ' -name \*.dylib | xargs rm -f');
    }
    $p->addLibrary($lib);
}

function install_freetype(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('freetype', '/usr/freetype'))
            ->withHomePage('https://freetype.org/')
            ->withUrl('https://download.savannah.gnu.org/releases/freetype/freetype-2.10.4.tar.gz')
            ->withLicense('https://gitlab.freedesktop.org/freetype/freetype/-/blob/master/docs/FTL.TXT', Library::LICENSE_SPEC)
            ->withConfigure("
                export ZLIB_CFLAGS=$(pkg-config --cflags zlib) ;
                export ZLIB_LIBS=$(pkg-config --libs zlib) ;

                export BZIP2_CFLAGS='-I/usr/bzip2/include'
                export BZIP2_LIBS='-L/usr/bzip2/lib -lbz2'

                export LIBPNG_LIBS=$(pkg-config --cflags libpng libpng16) ;
                export LIBPNG_LIBS=$(pkg-config --libs libpng libpng16) ;
                
               ./configure --prefix=/usr/freetype --enable-static --disable-shared \
               --with-zlib=yes \
               --with-bzip2=yes \
               --with-png=yes \
               --with-harfbuzz=no \
               --with-brotli=no
            ")
            ->withLdflags('-L/usr/freetype/lib/')
            ->withPkgConfig('/usr/freetype/lib/pkgconfig')
            ->withPkgName('freetype2')

    );
}

function install_libwebp(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libwebp', '/usr/libwebp'))
            ->withUrl('https://codeload.github.com/webmproject/libwebp/tar.gz/refs/tags/v1.2.1')
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr/libwebp --enable-static --disable-shared')
            ->withFile('libwebp-1.2.1.tar.gz')
            ->withHomePage('https://github.com/webmproject/libwebp')
            ->withLicense('https://github.com/webmproject/libwebp/blob/main/COPYING', Library::LICENSE_SPEC)
    );
}

function install_sqlite3(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('sqlite3'))
            ->withUrl('https://www.sqlite.org/2021/sqlite-autoconf-3370000.tar.gz')
            ->withHomePage('https://www.sqlite.org/index.html')
            ->withLicense('https://www.sqlite.org/copyright.html', Library::LICENSE_SPEC)
            ->withConfigure('./configure --prefix=/usr/sqlite3/ --enable-static --disable-shared')
            ->withPkgConfig('/usr/sqlite3/lib/pkgconfig')
            ->withLdflags('-L/usr/sqlite3/lib')
            ->withPkgName('sqlite3')
    );
}

function install_zlib(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('zlib'))
            ->withUrl('https://www.zlib.net/zlib-1.2.13.tar.gz')
            ->withHomePage('https://zlib.net/')
            ->withLicense('https://zlib.net/zlib_license.html', Library::LICENSE_SPEC)
            ->withConfigure('./configure --prefix=/usr/zlib --static')
            ->withPkgConfig('/usr/zlib/lib/pkgconfig')
            ->withPkgName('zlib')
            ->withLdflags('-L/usr/zlib/lib')
    );
}

function install_bzip2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('bzip2', '/usr/bzip2'))
            ->withUrl('https://sourceware.org/pub/bzip2/bzip2-1.0.8.tar.gz')
            ->withHomePage('https://www.sourceware.org/bzip2/')
            ->withLicense('https://www.sourceware.org/bzip2/', Library::LICENSE_BSD)
            ->withMakeOptions('all')
            ->withMakeInstallOptions(' install PREFIX=/usr/bzip2')
            ->disableDefaultPkgConfig()
            ->withLdflags('-L/usr/bzip2/lib')
    );
}

function install_icu(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('icu'))
            ->withUrl('https://github.com/unicode-org/icu/releases/download/release-60-3/icu4c-60_3-src.tgz')
            ->withHomePage('https://icu.unicode.org/')
            ->withLicense('https://github.com/unicode-org/icu/blob/main/icu4c/LICENSE', Library::LICENSE_SPEC)
            ->withManual("https://unicode-org.github.io/icu/userguide/icu4c/build.html")
            ->withCleanBuildDirectory()
            ->withConfigure('
              source/runConfigureICU Linux --help

             CPPFLAGS="-DU_CHARSET_IS_UTF8=1  -DU_USING_ICU_NAMESPACE=1  -DU_STATIC_IMPLEMENTATION=1"

             source/runConfigureICU Linux --prefix=/usr/icu \
             --enable-icu-config=no \
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
             ')
            ->withMakeOptions('all VERBOSE=1')
            ->withPkgName('icu-uc icu-io icu-i18n')
            ->withPkgConfig('/usr/icu/lib/pkgconfig')
            ->withLdflags('-L/usr/icu/lib')
    );
}

function install_oniguruma(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('oniguruma'))
            ->withUrl('https://codeload.github.com/kkos/oniguruma/tar.gz/refs/tags/v6.9.7')
            ->withFile('oniguruma-6.9.7.tar.gz')
            ->withLicense('https://github.com/kkos/oniguruma/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withConfigure('
            ./autogen.sh && ./configure --prefix=/usr/oniguruma --enable-static --disable-shared
            ')
            ->withPkgConfig('/usr/oniguruma/lib/pkgconfig')
            ->withPkgName('oniguruma')
            ->withLdflags('-L/usr/oniguruma/lib')
    );
}

// MUST be in the /usr directory
function install_zip(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('zip'))
            ->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
            ->withHomePage('https://libzip.org/')
            ->withLicense('https://libzip.org/license/', Library::LICENSE_BSD)
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure("
                mkdir -p build 
                cd build 
            ")
            ->withConfigure(
                '
                 cmake ..  \
                -DCMAKE_INSTALL_PREFIX=/usr/zip  \
                -DBUILD_TOOLS=OFF \
                -DBUILD_EXAMPLES=OFF \
                -DBUILD_DOC=OFF \
                -DLIBZIP_DO_INSTALL=ON \
                -DBUILD_SHARED_LIBS=OFF \
                -DENABLE_GNUTLS=OFF  \
                -DENABLE_MBEDTLS=OFF \
                -DENABLE_OPENSSL=ON \
                -DOPENSSL_USE_STATIC_LIBS=TRUE \
                -DOPENSSL_LIBRARIES=/usr/openssl/lib \
                -DOPENSSL_INCLUDE_DIR=/usr/openssl/include \
                -DZLIB_LIBRARY=/usr/zlib/lib \
                -DZLIB_INCLUDE_DIR=/usr/zlib/include \
                -DENABLE_BZIP2=ON \
                -DBZIP2_LIBRARIES=/usr/bzip2/lib \
                -DBZIP2_LIBRARY=/usr/bzip2/lib \
                -DBZIP2_INCLUDE_DIR=/usr/bzip2/include \
                -DBZIP2_NEED_PREFIX=ON \
                -DENABLE_LZMA=OFF  \
                -DENABLE_ZSTD=OFF
            '
            )
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
            ->withMakeOptions('VERBOSE=1 all  ') //VERBOSE=1
            ->withMakeInstallOptions("VERBOSE=1 install PREFIX=/usr/zip")
            ->withPkgName('libzip')
            ->withPkgConfig('/usr/zip/lib/pkgconfig')
            ->withLdflags('-L/usr/zip/lib')
    );
}

function install_cares(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('cares'))
            ->withUrl('https://c-ares.org/download/c-ares-1.18.1.tar.gz')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withHomePage('https://c-ares.org/')
            ->withConfigure('./configure --prefix=/usr/cares --enable-static --disable-shared ')
            ->withPkgName('libcares')
            ->withPkgConfig('/usr/cares/lib/pkgconfig')
            ->withLdflags('-L/usr/cares/lib')
            ->withBinPath('/usr/cares/bin/')
    );
}

function install_readline(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('readline', '/usr/readline'))
            ->withUrl('https://ftp.gnu.org/gnu/readline/readline-8.2.tar.gz')
            ->withLicense('http://www.gnu.org/licenses/gpl.html', Library::LICENSE_GPL)
            ->withHomePage('https://tiswww.case.edu/php/chet/readline/rltop.html')
            ->withManual('https://tiswww.case.edu/php/chet/readline/rltop.html')
            ->withCleanBuildDirectory()
            ->withConfigure('
            ./configure \
            --prefix=/usr/readline \
            --enable-static \
            --disable-shared \
            --with-curses \
            --enable-multibyte
            ')
            ->withPkgName('readline')
            ->withLdflags('-L/usr/readline/lib')
            ->withBinPath('/usr/readline/bin')
            ->withLabel('library')
    );
}

function install_libedit(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libedit', '/usr/libedit'))
            ->withUrl('https://thrysoee.dk/editline/libedit-20210910-3.1.tar.gz')
            ->withConfigure('./configure --prefix=/usr/libedit --enable-static --disable-shared')
            ->withLdflags('')
            ->withLicense('http://www.netbsd.org/Goals/redistribution.html', Library::LICENSE_BSD)
            ->withHomePage('https://thrysoee.dk/editline/')
    );
}

function install_ncurses(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('ncurses'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withLicense('https://invisible-island.net/ncurses/ncurses-license.html', Library::LICENSE_MIT)
            ->withHomePage('http://www.gnu.org/software/ncurses/ncurses.html')
            ->withManual('https://invisible-island.net/ncurses/announce.html#h3-documentation')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                '
                test -d /usr/ncurses/ && rm -rf /usr/ncurses/ ;
                mkdir -p /usr/ncurses/lib/pkgconfig
            '
            )
            ->withConfigure(
                '
                
            ./configure \
            --prefix=/usr/ncurses \
            --enable-static \
            --disable-shared \
            --enable-pc-files \
            --with-pkg-config=/usr/ncurses/lib/pkgconfig \
            --with-pkg-config-libdir=/usr/ncurses/lib/pkgconfig \
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
            '
            )
            ->withScriptBeforeInstall('
            ln -s /usr/ncurses/lib/pkgconfig/formw.pc /usr/ncurses/lib/pkgconfig/form.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/menuw.pc /usr/ncurses/lib/pkgconfig/menu.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/ncurses++w.pc /usr/ncurses/lib/pkgconfig/ncurses++.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/ncursesw.pc /usr/ncurses/lib/pkgconfig/ncurses.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/panelw.pc /usr/ncurses/lib/pkgconfig/panel.pc ;
            ln -s /usr/ncurses/lib/pkgconfig/ticw.pc /usr/ncurses/lib/pkgconfig/tic.pc ;

            ln -s /usr/ncurses/lib/libformw.a /usr/ncurses/lib/libform.a ;
            ln -s /usr/ncurses/lib/libmenuw.a /usr/ncurses/lib/libmenu.a ;
            ln -s /usr/ncurses/lib/libncurses++w.a /usr/ncurses/lib/libncurses++.a ;
            ln -s /usr/ncurses/lib/libncursesw.a /usr/ncurses/lib/libncurses.a ;
            ln -s /usr/ncurses/lib/libpanelw.a  /usr/ncurses/lib/libpanel.a ;
            ln -s /usr/ncurses/lib/libticw.a /usr/ncurses/lib/libtic.a ;
            ')
            ->withPkgName('ncursesw')
            ->withPkgConfig('/usr/ncurses/lib/pkgconfig')
            ->withLdflags('-L/usr/ncurses/lib/')
            ->withBinPath('/usr/ncurses/bin')
    );
}


function install_libsodium(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libsodium'))
            ->withUrl('https://download.libsodium.org/libsodium/releases/libsodium-1.0.18.tar.gz')
            // ISC License, like BSD
            ->withLicense('https://en.wikipedia.org/wiki/ISC_license', Library::LICENSE_SPEC)
            ->withHomePage('https://doc.libsodium.org/')
            ->withConfigure('./autogen.sh && ./configure --prefix=/usr/libsodium --enable-static --disable-shared')
            ->withPkgConfig('/usr/libsodium/lib/pkgconfig')
            ->withPkgName('libsodium')
            ->withLdflags('-L/usr/libsodium/lib')
    );
}

function install_libyaml(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libyaml', '/usr/libyaml'))
            ->withUrl('https://pyyaml.org/download/libyaml/yaml-0.2.5.tar.gz')
            ->withConfigure('./configure --prefix=/usr/libyaml --enable-static --disable-shared')
            ->withPkgConfig('/usr/libyaml/lib/pkgconfig')
            ->withPkgName('yaml-0.1')
            ->withLdflags('-L/usr/libyaml/lib')
            ->withPkgName('yaml-0.1')
            ->withLicense('https://pyyaml.org/wiki/LibYAML', Library::LICENSE_MIT)
            ->withHomePage('https://pyyaml.org/wiki/LibYAML')
    );
}

function install_brotli(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('brotli', '/usr/brotli'))
            ->withHomePage('https://github.com/google/brotli')
            ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
            ->withFile('brotli-1.0.9.tar.gz')
            ->withConfigure(
                "
                 cmake . -DCMAKE_BUILD_TYPE=Release \
                 -DBUILD_SHARED_LIBS=OFF \
                 -DCMAKE_INSTALL_PREFIX=/usr/brotli
                "
            )
            ->withScriptAfterInstall(
                '
                    rm -rf /usr/brotli/lib/*.so.*
                    rm -rf /usr/brotli/lib/*.so
                    rm -rf /usr/brotli/lib/*.dylib
                    cp -f  /usr/brotli/lib/libbrotlicommon-static.a /usr/brotli/lib/libbrotli.a
                    cp -f /usr/brotli/lib/libbrotlienc-static.a /usr/brotli/lib/libbrotlienc.a
                    cp -f /usr/brotli/lib/libbrotlidec-static.a /usr/brotli/lib/libbrotlidec.a
                '
            )
            ->withPkgConfig('/usr/brotli/lib/pkgconfig')
            ->withPkgName('libbrotlicommon libbrotlidec libbrotlienc')
            ->withLdflags('-L/usr/brotli/lib')
    );
}

function install_curl(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('curl', '/usr/curl'))
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withHomePage('https://curl.se/')
            ->withUrl('https://curl.se/download/curl-7.80.0.tar.gz')

            ->withConfigure(
                '
                  autoreconf -fi && ./configure --prefix=/usr/curl --enable-static --disable-shared \
                 --with-openssl=/usr/openssl \
                 --without-librtmp \
                 --without-brotli \
                 --without-libidn2  \
                 --without-zstd \
                 --disable-ldap \
                 --disable-rtsp  \
                 --without-nghttp2 \
                 --without-nghttp3
            '
            )
            ->withPkgName('libcurl')
            ->withPkgConfig('/usr/curl/lib/pkgconfig')
            ->withLdflags('-L/usr/curl/lib')

    );
}

function install_mimalloc(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('mimalloc', '/usr/mimalloc'))
            ->withUrl('https://github.com/microsoft/mimalloc/archive/refs/tags/v2.0.7.tar.gz')
            ->withFile('mimalloc-2.0.7.tar.gz')
            ->withLicense('https://github.com/microsoft/mimalloc/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://microsoft.github.io/mimalloc/')
            ->withConfigure(
                '
                cmake . -DCMAKE_INSTALL_PREFIX=/usr/mimalloc \
                -DMI_BUILD_SHARED=OFF \
                -DMI_INSTALL_TOPLEVEL=ON \
                -DMI_PADDING=OFF \
                -DMI_SKIP_COLLECT_ON_EXIT=ON \
                -DMI_BUILD_TESTS=OFF
            '
            )
            ->withLdflags('-L/usr/mimalloc/lib -lmimalloc')
            ->withPkgName('mimalloc')
            ->withPkgConfig('/usr/mimalloc/lib/pkgconfig')
            ->withLdflags('-L/usr/mimalloc/lib')
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
            # ./configure --help
            
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
    $workDir=$p->getWorkDir();;
    $command = '';
    if ($p->getOsType() === 'macos') {

        $command = <<<EOF
        #  config.m4.backup不存在执行 才执行后面命令 (因为不能多次删除指定行）
        test -f {$workDir}/ext/curl/config.m4.backup ||  sed -i.backup '75,82d' {$workDir}/ext/curl/config.m4

EOF;

    }
    $p->addLibrary(
        (new Library('php_internal_extensions'))
            ->withHomePage('https://www.php.net/')
            ->withLicense('http://github.com/libffi/libffi/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/php/php-src/archive/refs/tags/php-8.1.12.tar.gz')
            ->withFile('php-8.1.12.tar.gz')
            ->withManual('https://www.php.net/docs.php')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                "
                    pwd
                    test -d {$workDir}/ext/ffi && rm -rf {$workDir}/ext/ffi
                    cp -rf  ext/ffi {$workDir}/ext/
                    
                    test -d {$workDir}/ext/pdo_pgsql && rm -rf {$workDir}/ext/pdo_pgsql
                    cp -rf  ext/pdo_pgsql {$workDir}/ext/
                    
                    test -d {$workDir}/ext/pgsql && rm -rf {$workDir}/ext/pgsql
                    cp -rf  ext/pgsql {$workDir}/ext/
                    
                    
                " . $command
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
            ->withUrl('https://github.com/lz4/lz4/archive/refs/tags/v1.9.4.tar.gz')
            ->withFile('lz4-v1.9.4.tar.gz')
            ->withPkgName('liblz4')
            ->withScriptBeforeConfigure("test -d /usr/liblz4/ && rm -rf /usr/liblz4/ ;")
            ->withMakeInstallOptions("prefix=/usr/liblz4/ install ")
            ->withPkgConfig('/usr/liblz4/lib/pkgconfig')
            ->withLdflags('-L/usr/liblz4/lib')
            ->withHomePage('https://github.com/lz4/lz4.git')
            ->withLicense('https://github.com/lz4/lz4/blob/dev/LICENSE', Library::LICENSE_GPL)
    );
}

function install_liblzma(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('liblzma'))
            //->withUrl('https://tukaani.org/xz/xz-5.2.9.tar.gz')
            ->withUrl('https://jaist.dl.sourceforge.net/project/lzmautils/xz-5.4.1.tar.xz')
            ->withFile('xz-5.2.9.tar.gz')
            ->withConfigure('./configure --prefix=/usr/liblzma/ --enable-static  --disable-shared --disable-doc')
            ->withPkgName('liblzma')
            ->withPkgConfig('/usr/liblzma/lib/pkgconfig')
            ->withLdflags('-L/usr/liblzma/lib')
            ->withHomePage('https://tukaani.org/xz/')
            ->withLicense('https://git.tukaani.org/?p=xz.git;a=blob;f=COPYING', Library::LICENSE_LGPL)
    );
}

function install_libzstd(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libzstd'))
            ->withUrl('https://github.com/facebook/zstd/releases/download/v1.5.2/zstd-1.5.2.tar.gz')
            ->withFile('zstd-1.5.2.tar.gz')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                '
            test -d /usr/libzstd/ && rm -rf /usr/libzstd/
            mkdir -p build/cmake/builddir
            '
            )
            ->withConfigure(
                '
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
            ->withPkgConfig('/usr/libzstd/lib/pkgconfig')
            ->withLdflags('-L/usr/libzstd/lib')
            ->withHomePage('https://github.com/facebook/zstd')
            ->withLicense('https://github.com/facebook/zstd/blob/dev/COPYING', Library::LICENSE_GPL)
    );
}

function install_harfbuzz(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('harfbuzz', '/usr/brotli'))

            ->withLicense('https://github.com/harfbuzz/harfbuzz/blob/main/COPYING', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/harfbuzz/harfbuzz.git')
            ->withUrl('https://github.com/harfbuzz/harfbuzz/archive/refs/tags/6.0.0.tar.gz')
            ->withFile('harfbuzz-6.0.0.tar.gz')
            ->withLabel('library')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure('test -d /usr/harfbuzz/ && rm -rf /usr/harfbuzz/ ')
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
            "
            )
            ->withPkgName('libbrotlicommon libbrotlidec libbrotlienc')
            ->withPkgConfig('/usr/harfbuzz/lib/pkgconfig')
            ->withPkgName('libbrotlicommon libbrotlidec libbrotlienc')
            ->withLdflags('-L/usr/harfbuzz/lib')
            ->withSkipBuildInstall()
    );
}

function install_libidn2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('libidn2', '/usr/libidn2'))
            ->withUrl('https://ftp.gnu.org/gnu/libidn/libidn2-2.3.4.tar.gz')
            ->withPkgConfig('')
            ->withLdflags('-L/usr/libidn2/lib')
            ->withConfigure('./configure --prefix=/usr/libidn2 enable_static=yes enable_shared=no')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withSkipBuildInstall()
    );
}

function install_nghttp2(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('nghttp2', '/usr/nghttp2'))
            ->withUrl('https://github.com/nghttp2/nghttp2/releases/download/v1.51.0/nghttp2-1.51.0.tar.gz')
            ->withPkgConfig('')
            ->withLdflags('-L/usr/nghttp2/lib')
            ->withConfigure('./configure --prefix=/usr/nghttp2 enable_static=yes enable_shared=no')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withSkipBuildInstall()
    );
}

function install_php_extension_micro(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('php_extension_micro', ))
            ->withHomePage('https://github.com/dixyes/phpmicro')
            ->withUrl('https://github.com/dixyes/phpmicro/archive/refs/heads/master.zip')
            ->withFile('latest-phpmicro.zip')
            ->withLicense('https://github.com/dixyes/phpmicro/blob/master/LICENSE', Library::LICENSE_APACHE2)
            ->withManual('https://github.com/dixyes/phpmicro#readme')
            ->withLabel('extension')
            ->withCleanBuildDirectory()
            ->withUntarArchiveCommand('unzip')
            ->withScriptBeforeConfigure('return 0')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
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
            ->withLabel('env')
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
        (new Library('re2c', ))
            ->withHomePage('http://re2c.org/')
            ->withUrl('https://github.com/skvadrik/re2c/releases/download/3.0/re2c-3.0.tar.xz')
            ->withLicense('https://github.com/skvadrik/re2c/blob/master/LICENSE', Library::LICENSE_GPL)
            ->withManual('https://re2c.org/build/build.html')
            ->withLabel('env')
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