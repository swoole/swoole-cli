<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

// ================================================================================================
// Library
// ================================================================================================

/**
 * cmake use static openssl
 *
 * set(OPENSSL_USE_STATIC_LIBS TRUE)
 * find_package(OpenSSL REQUIRED)
 * target_link_libraries(program OpenSSL::Crypto)
 */

function install_openssl(Preprocessor $p)
{
    $openssl_prefix = OPENSSL_PREFIX;
    $p->addLibrary(
        (new Library('openssl'))
            ->withHomePage('https://www.openssl.org/')
            ->withUrl('https://www.openssl.org/source/openssl-1.1.1t.tar.gz')
            ->withLicense('https://github.com/openssl/openssl/blob/master/LICENSE.txt', Library::LICENSE_APACHE2)
            ->withPrefix($openssl_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($openssl_prefix)
            ->withConfigure(
                './config' . ($p->getOsType(
                ) === 'macos' ? '' : ' -static --static') . ' no-shared --prefix=' . $openssl_prefix
            )
            ->withMakeInstallCommand('install_sw')
            ->withPkgName('libcrypto libssl   openssl')
            ->withBinPath($openssl_prefix . '/bin/')
    );
}


function install_libiconv(Preprocessor $p)
{
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libiconv'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
            ->withPrefix($libiconv_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libiconv_prefix)
            ->withPkgConfig('')
            ->withConfigure('./configure --prefix=' . $libiconv_prefix . ' enable_static=yes enable_shared=no')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withBinPath($libiconv_prefix . '/bin/')
    );
}


// Dependent libiconv
function install_libxml2(Preprocessor $p)
{
    $libxml2_prefix = LIBXML2_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libxml2'))
            ->withUrl('https://gitlab.gnome.org/GNOME/libxml2/-/archive/v2.9.10/libxml2-v2.9.10.tar.gz')
            ->withPrefix($libxml2_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libxml2_prefix)
            ->withConfigure(
                <<<EOF
            ./autogen.sh && ./configure \
            --prefix=$libxml2_prefix \
            --with-iconv=$iconv_prefix \
            --enable-static=yes \
            --enable-shared=no \
            --without-python
EOF
            )
            ->withPkgName('libxml-2.0')
            ->withBinPath($libxml2_prefix . '/bin/')
            ->withLicense('https://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->depends('libiconv', 'liblzma')
    );
}

// Dependent libxml2
function install_libxslt(Preprocessor $p)
{
    // EXSLT 数学包提供了处理数值和比较节点的函数
    //https://developer.mozilla.org/en-US/docs/Web/EXSLT
    $libxslt_prefix = LIBXSLT_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $p->addLibrary(
        (new Library('libxslt'))
            ->withHomePage('https://gitlab.gnome.org/GNOME/libxslt/-/wikis/home')
            ->withUrl('https://gitlab.gnome.org/GNOME/libxslt/-/archive/v1.1.34/libxslt-v1.1.34.tar.gz')
            //https://download.gnome.org/sources/libxslt/1.1/
            ->withLicense('http://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->withPrefix($libxslt_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libxslt_prefix)
            ->withConfigure(
                <<<EOF
            ./autogen.sh
           ./configure --help
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static libxml-2.0  )" \
            LDFLAGS="$(pkg-config --libs-only-L      --static libxml-2.0  )" \
            LIBS="$(pkg-config --libs-only-l         --static libxml-2.0  )" \
            ./configure \
            --prefix={$libxslt_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --with-libxml-libs-prefix={$libxml2_prefix} \
            --without-python \
            --without-crypto \
            --without-profiler \
            --without-plugins \
            --without-debugger
EOF
            )
            ->withPkgName('libexslt')
            ->withPkgName('libxslt')
            ->withBinPath($libxslt_prefix . '/bin/')
            ->depends('libxml2', 'libiconv')
    );
}


function install_brotli(Preprocessor $p)
{
    /*
    -DCMAKE_BUILD_TYPE="${BUILD_TYPE}" \
    -DCMAKE_INSTALL_PREFIX="${PREFIX}" \
    -DCMAKE_INSTALL_LIBDIR="${LIBDIR}" \

    -Wno-dev
  */
    $brotli_prefix = BROTLI_PREFIX;
    $p->addLibrary(
        (new Library('brotli'))
            ->withHomePage('https://github.com/google/brotli')
            ->withLicense('https://github.com/google/brotli/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withManual('https://github.com/google/brotli')//有多种构建方式，选择cmake 构建
            ->withUrl('https://github.com/google/brotli/archive/refs/tags/v1.0.9.tar.gz')
            ->withFile('brotli-1.0.9.tar.gz')
            ->withPrefix($brotli_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($brotli_prefix)
            ->withBuildScript(
                <<<EOF
            cmake . -DCMAKE_BUILD_TYPE=Release \
            -DCMAKE_INSTALL_PREFIX={$brotli_prefix} \
            -DBROTLI_SHARED_LIBS=OFF \
            -DBROTLI_STATIC_LIBS=ON \
            -DBROTLI_DISABLE_TESTS=OFF \
            -DBROTLI_BUNDLED_MODE=OFF \
            && \
            cmake --build . --config Release --target install
EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$brotli_prefix}/lib/*.so.*
            rm -rf {$brotli_prefix}/lib/*.so
            rm -rf {$brotli_prefix}/lib/*.dylib
            cp  -f {$brotli_prefix}/lib/libbrotlicommon-static.a {$brotli_prefix}/lib/libbrotli.a
            mv     {$brotli_prefix}/lib/libbrotlicommon-static.a {$brotli_prefix}/lib/libbrotlicommon.a
            mv     {$brotli_prefix}/lib/libbrotlienc-static.a    {$brotli_prefix}/lib/libbrotlienc.a
            mv     {$brotli_prefix}/lib/libbrotlidec-static.a    {$brotli_prefix}/lib/libbrotlidec.a
EOF
            )
            ->withPkgName('libbrotlicommon')
            ->withPkgName('libbrotlidec')
            ->withPkgName('libbrotlienc')
            ->withBinPath($brotli_prefix . '/bin/')
    );
}

function install_cares(Preprocessor $p)
{
    $cares_prefix = CARES_PREFIX;
    $p->addLibrary(
        (new Library('cares'))
            ->withUrl('https://c-ares.org/download/c-ares-1.19.0.tar.gz')
            ->withPrefix($cares_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($cares_prefix)
            ->withConfigure("./configure --prefix={$cares_prefix} --enable-static --disable-shared")
            ->withPkgName('libcares')
            ->withLicense('https://c-ares.org/license.html', Library::LICENSE_MIT)
            ->withHomePage('https://c-ares.org/')
    );
}


function install_gmp(Preprocessor $p)
{
    $gmp_prefix = GMP_PREFIX;
    $p->addLibrary(
        (new Library('gmp'))
            ->withUrl('https://gmplib.org/download/gmp/gmp-6.2.1.tar.lz')
            ->withPrefix($gmp_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($gmp_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix=$gmp_prefix \
            --enable-static=yes \
            --enable-static=no
EOF
            )
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withPkgName('gmp')
    );
}


/*
// CFLAGS="-static -O2 -Wall" \
// LDFLAGS="-Wl,R-lncurses"
// LDFLAGS="-lncurses" \
 */
function install_ncurses(Preprocessor $p)
{
    $ncurses_prefix = NCURSES_PREFIX;
    $p->addLibrary(
        (new Library('ncurses'))
            ->withUrl('https://ftp.gnu.org/pub/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withMirrorUrl('https://mirrors.tuna.tsinghua.edu.cn/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withMirrorUrl('https://mirrors.ustc.edu.cn/gnu/ncurses/ncurses-6.3.tar.gz')
            ->withPrefix($ncurses_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($ncurses_prefix)
            ->withConfigure(
                <<<EOF
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
            ->withScriptBeforeInstall(
                '
                ln -s ' . $ncurses_prefix . '/lib/pkgconfig/formw.pc ' . $ncurses_prefix . '/lib/pkgconfig/form.pc ;
                ln -s ' . $ncurses_prefix . '/lib/pkgconfig/menuw.pc ' . $ncurses_prefix . '/lib/pkgconfig/menu.pc ;
                ln -s ' . $ncurses_prefix . '/lib/pkgconfig/ncurses++w.pc ' . $ncurses_prefix . '/lib/pkgconfig/ncurses++.pc ;
                ln -s ' . $ncurses_prefix . '/lib/pkgconfig/ncursesw.pc ' . $ncurses_prefix . '/lib/pkgconfig/ncurses.pc ;
                ln -s ' . $ncurses_prefix . '/lib/pkgconfig/panelw.pc ' . $ncurses_prefix . '/lib/pkgconfig/panel.pc ;
                ln -s ' . $ncurses_prefix . '/lib/pkgconfig/ticw.pc ' . $ncurses_prefix . '/lib/pkgconfig/tic.pc ;

                ln -s ' . $ncurses_prefix . '/lib/libformw.a ' . $ncurses_prefix . '/lib/libform.a ;
                ln -s ' . $ncurses_prefix . '/lib/libmenuw.a ' . $ncurses_prefix . '/lib/libmenu.a ;
                ln -s ' . $ncurses_prefix . '/lib/libncurses++w.a ' . $ncurses_prefix . '/lib/libncurses++.a ;
                ln -s ' . $ncurses_prefix . '/lib/libncursesw.a ' . $ncurses_prefix . '/lib/libncurses.a ;
                ln -s ' . $ncurses_prefix . '/lib/libpanelw.a  ' . $ncurses_prefix . '/lib/libpanel.a ;
                ln -s ' . $ncurses_prefix . '/lib/libticw.a ' . $ncurses_prefix . '/lib/libtic.a ;
            '
            )
            ->withPkgName('ncursesw')
            ->withLicense('https://github.com/projectceladon/libncurses/blob/master/README', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/projectceladon/libncurses')
            ->withBinPath($ncurses_prefix . '/bin/')
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
            ->withPrefix($readline_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($readline_prefix)
            ->withConfigure(
                <<<EOF
                ./configure \
                --prefix={$readline_prefix} \
                --enable-static \
                --disable-shared \
                --with-curses \
                --enable-multibyte
EOF
            )
            ->withPkgName('readline')
            ->withLdflags('-L' . $readline_prefix . '/lib')
            ->withLicense('https://www.gnu.org/licenses/gpl.html', Library::LICENSE_GPL)
            ->withHomePage('https://tiswww.case.edu/php/chet/readline/rltop.html')
            ->withBinPath($readline_prefix . '/bin/')
            ->depends('ncurses')
    );
}


function install_libyaml(Preprocessor $p): void
{
    $libyaml_prefix = LIBYAML_PREFIX;
    $p->addLibrary(
        (new Library('libyaml'))
            ->withUrl('https://pyyaml.org/download/libyaml/yaml-0.2.5.tar.gz')
            ->withPrefix($libyaml_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libyaml_prefix)
            ->withConfigure('./configure --prefix=' . $libyaml_prefix . ' --enable-static --disable-shared')
            ->withPkgName('yaml-0.1')
            ->withLicense('https://pyyaml.org/wiki/LibYAML', Library::LICENSE_MIT)
            ->withHomePage('https://pyyaml.org/wiki/LibYAML')
    );
}

function install_libsodium(Preprocessor $p)
{
    $libsodium_prefix = LIBSODIUM_PREFIX;
    $p->addLibrary(
        (new Library('libsodium'))
            // ISC License, like BSD
            ->withLicense('https://en.wikipedia.org/wiki/ISC_license', Library::LICENSE_SPEC)
            ->withHomePage('https://doc.libsodium.org/')
            ->withUrl('https://download.libsodium.org/libsodium/releases/libsodium-1.0.18.tar.gz')
            ->withPrefix($libsodium_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libsodium_prefix)
            ->withConfigure('./configure --prefix=' . $libsodium_prefix . ' --enable-static --disable-shared')
            ->withPkgName('libsodium')
    );
}

function install_bzip2(Preprocessor $p)
{
    $libbzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('bzip2'))
            ->withHomePage('https://www.sourceware.org/bzip2/')
            ->withLicense('https://www.sourceware.org/bzip2/', Library::LICENSE_BSD)
            ->withUrl('https://sourceware.org/pub/bzip2/bzip2-1.0.8.tar.gz')
            ->withManual('https://sourceware.org/git/bzip2.git')
            ->withPrefix($libbzip2_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libbzip2_prefix)
            ->withMakeOptions('PREFIX=' . $libbzip2_prefix)
            ->withMakeInstallOptions('PREFIX=' . $libbzip2_prefix)
            ->withBinPath($libbzip2_prefix . '/bin/')
    );
}

function install_zlib(Preprocessor $p)
{
    $zlib_prefix = ZLIB_PREFIX;
    $p->addLibrary(
        (new Library('zlib'))
            //->withUrl('https://zlib.net/zlib-1.2.13.tar.gz')
            ->withUrl('https://udomain.dl.sourceforge.net/project/libpng/zlib/1.2.11/zlib-1.2.11.tar.gz')
            ->withPrefix($zlib_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($zlib_prefix)
            ->withConfigure('./configure --prefix=' . $zlib_prefix . ' --static')
            ->withHomePage('https://zlib.net/')
            ->withLicense('https://zlib.net/zlib_license.html', Library::LICENSE_SPEC)
            ->withPkgName('zlib')
            ->depends('libxml2', 'bzip2')
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
            ->withFile('lz4-v1.9.4.tar.gz')
            ->withPkgName('liblz4')
            ->withPrefix($liblz4_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($liblz4_prefix)
            ->withConfigure(
                <<<EOF
            cd build/cmake/
            cmake . -DCMAKE_INSTALL_PREFIX={$liblz4_prefix}  -DBUILD_SHARED_LIBS=OFF  -DBUILD_STATIC_LIBS=ON
EOF
            )
            ->withPkgName('liblz4')
            ->withBinPath($liblz4_prefix . '/bn/')
    );

    //可以使用CMAKE 编译 也可以
    //不使用CMAKE，需要自己修改安装目录
    //->withMakeOptions('INSTALL_PROGRAM=/usr/liblz4/')
    //->withMakeInstallOptions("DESTDIR=/usr/liblz4/")
}


function install_liblzma(Preprocessor $p)
{
    $liblzma_prefix = LIBLZMA_PREFIX;
    $p->addLibrary(
        (new Library('liblzma'))
            ->withHomePage('https://tukaani.org/xz/')
            ->withLicense('https://github.com/tukaani-project/xz/blob/master/COPYING.GPLv3', Library::LICENSE_LGPL)
            //->withUrl('https://tukaani.org/xz/xz-5.2.9.tar.gz')
            //->withFile('xz-5.2.9.tar.gz')
            ->withUrl('https://github.com/tukaani-project/xz/releases/download/v5.4.1/xz-5.4.1.tar.gz')
            ->withFile('xz-5.4.1.tar.gz')
            ->withPrefix($liblzma_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($liblzma_prefix)
            ->withConfigure(
                './configure --prefix=' . $liblzma_prefix . ' --enable-static  --disable-shared --disable-doc'
            )
            ->withPkgName('liblzma')
            ->withBinPath($liblzma_prefix . '/bin/')
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
            ->withCleanPreInstallDirectory($libzstd_prefix)
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
            ->withBinPath($libzstd_prefix . '/bin/')
            ->withPkgName('libzstd')
            ->depends('liblz4', 'liblzma')
    );
    /*
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
    */
}


// MUST be in the /usr directory
function install_libzip(Preprocessor $p)
{
    $openssl_prefix = OPENSSL_PREFIX;
    $libzip_prefix = ZIP_PREFIX;
    $liblzma_prefix = LIBLZMA_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('libzip'))
            //->withUrl('https://libzip.org/download/libzip-1.8.0.tar.gz')
            ->withUrl('https://libzip.org/download/libzip-1.9.2.tar.gz')
            ->withManual('https://libzip.org/')
            ->withPrefix($libzip_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libzip_prefix)
            ->withConfigure(
                <<<EOF
            cmake -Wno-dev .  \
            -DCMAKE_INSTALL_PREFIX={$libzip_prefix} \
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
            ->depends('openssl', 'zlib', 'bzip2', 'liblzma', 'libzstd')
    );
}


function install_sqlite3(Preprocessor $p)
{
    $sqlite_prefix = SQLITE3_PREFIX;
    $p->addLibrary(
        (new Library('sqlite3'))
            ->withUrl('https://www.sqlite.org/2021/sqlite-autoconf-3370000.tar.gz')
            ->withPrefix($sqlite_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($sqlite_prefix)
            ->withConfigure('./configure --prefix=' . $sqlite_prefix . ' --enable-static --disable-shared')
            ->withHomePage('https://www.sqlite.org/index.html')
            ->withLicense('https://www.sqlite.org/copyright.html', Library::LICENSE_SPEC)
            ->withPkgName('sqlite3')
            ->withBinPath($sqlite_prefix . '/bin/')
    );
}


function install_icu(Preprocessor $p)
{
    /*
     --with-data-packaging     specify how to package ICU data. Possible values:

        files    raw files (.res, etc)
        archive  build a single icudtXX.dat file
        library  shared library (.dll/.so/etc.)
        static   static library (.a/.lib/etc.)
        auto     build shared if possible (default)
    */
    $icu_prefix = ICU_PREFIX;
    $os = $p->getOsType() == 'macos' ? 'MacOSX' : 'Linux';
    $p->addLibrary(
        (new Library('icu'))
            ->withHomePage('https://icu.unicode.org/')
            ->withLicense('https://github.com/unicode-org/icu/blob/main/icu4c/LICENSE', Library::LICENSE_SPEC)
            ->withUrl('https://github.com/unicode-org/icu/releases/download/release-60-3/icu4c-60_3-src.tgz')
            ->withPrefix($icu_prefix)
            ->withConfigure(
                <<<EOF
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
            ->withPkgName('icu-i18n')
            ->withPkgName('icu-io')
            ->withPkgName('icu-uc')
            ->withBinPath($icu_prefix . '/bin/')
    );
}

function install_oniguruma(Preprocessor $p)
{
    $oniguruma_prefix = ONIGURUMA_PREFIX;
    $p->addLibrary(
        (new Library('oniguruma'))
            ->withUrl('https://codeload.github.com/kkos/oniguruma/tar.gz/refs/tags/v6.9.7')
            ->withPrefix($oniguruma_prefix)
            ->withConfigure(
                './autogen.sh && ./configure --prefix=' . $oniguruma_prefix . ' --enable-static --disable-shared'
            )
            ->withFile('oniguruma-6.9.7.tar.gz')
            ->withLicense('https://github.com/kkos/oniguruma/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withPkgName('oniguruma')
            ->withBinPath($oniguruma_prefix . '/bin/')
    );
}

function install_mimalloc(Preprocessor $p)
{
    $mimalloc_prefix = MIMALLOC_PREFIX;
    $p->addLibrary(
        (new Library('mimalloc'))
            ->withUrl('https://github.com/microsoft/mimalloc/archive/refs/tags/v2.0.7.tar.gz')
            ->withFile('mimalloc-2.0.7.tar.gz')
            ->withPrefix($mimalloc_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($mimalloc_prefix)
            ->withConfigure(
                'cmake . -DMI_BUILD_SHARED=OFF -DCMAKE_INSTALL_PREFIX=' . $mimalloc_prefix . ' -DMI_INSTALL_TOPLEVEL=ON -DMI_PADDING=OFF -DMI_SKIP_COLLECT_ON_EXIT=ON -DMI_BUILD_TESTS=OFF'
            )
            ->withPkgName('libmimalloc')
            ->withLicense('https://github.com/microsoft/mimalloc/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withHomePage('https://microsoft.github.io/mimalloc/')
            ->withLdflags('-L' . $mimalloc_prefix . '/lib -lmimalloc')
            ->disablePkgName()
    );
}

function install_libidn2(Preprocessor $p)
{
    $libidn2_prefix = LIBIDN2_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libidn2'))
            ->withUrl('https://ftp.gnu.org/gnu/libidn/libidn2-2.3.4.tar.gz')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withPrefix($libidn2_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libidn2_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help

            #  intl  依赖  gettext
            # 解决依赖  apk add  gettext  coreutils

            ./configure --prefix={$libidn2_prefix} \
            enable_static=yes \
            enable_shared=no \
            --disable-doc \
            --with-libiconv-prefix={$libiconv_prefix} \
            --with-libintl-prefix

EOF
            )
            ->withPkgName('libidn2')
            ->withBinPath($libidn2_prefix . '/bin/')
            ->depends('libiconv')
    );
}


/**
 *
 * -lz      压缩库（Z）
 *
 * -lrt     实时库（real time）：shm_open系列
 *
 * -lm     数学库（math）
 *
 * -lc     标准C库（C lib）
 *
 * -dl ，是显式加载动态库的动态函数库
 *
 */
/**
 * cur  交叉编译
 *
 * https://curl.se/docs/install.html
 *
 * export PATH=$PATH:/opt/hardhat/devkit/ppc/405/bin
 * export CPPFLAGS="-I/opt/hardhat/devkit/ppc/405/target/usr/include"
 * export AR=ppc_405-ar
 * export AS=ppc_405-as
 * export LD=ppc_405-ld
 * export RANLIB=ppc_405-ranlib
 * export CC=ppc_405-gcc
 * export NM=ppc_405-nm
 * --with-random=/dev/urandom
 *
 * randlib
 * strip
 *
 */
function install_curl(Preprocessor $p)
{
    //http3 有多个实现
    //参考文档： https://curl.se/docs/http3.html
    //https://curl.se/docs/protdocs.html
    $curl_prefix = CURL_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;

    $libidn2_prefix = LIBIDN2_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $cares_prefix = CARES_PREFIX;
    $brotli_prefix = BROTLI_PREFIX;
    $p->addLibrary(
        (new Library('curl'))
            ->withHomePage('https://curl.se/')
            ->withUrl('https://curl.se/download/curl-7.88.0.tar.gz')
            ->withManual('https://curl.se/docs/install.html')
            ->withLicense('https://github.com/curl/curl/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withPrefix($curl_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($curl_prefix)
            ->withConfigure(
                <<<EOF
            packages="zlib libbrotlicommon  libbrotlidec  libbrotlienc openssl libcares libidn2 libnghttp2"
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$packages )" \
            LDFLAGS="$(pkg-config --libs-only-L      --static \$packages )" \
            LIBS="$(pkg-config --libs-only-l         --static \$packages )" \
            ./configure --prefix={$curl_prefix}  \
            --enable-static \
            --disable-shared \
            --without-librtmp \
            --disable-ldap \
            --disable-rtsp \
            --enable-http \
            --enable-alt-svc \
            --enable-hsts \
            --enable-http-auth \
            --enable-mime \
            --enable-cookies \
            --enable-doh \
            --enable-threaded-resolver \
            --enable-ipv6 \
            --enable-proxy  \
            --enable-websockets \
            --enable-get-easy-options \
            --enable-file \
            --enable-mqtt \
            --enable-unix-sockets  \
            --enable-progress-meter \
            --enable-optimize \
            --with-zlib={$zlib_prefix} \
            --with-openssl={$openssl_prefix} \
            --with-libidn2={$libidn2_prefix} \
            --with-zstd={$libzstd_prefix} \
            --enable-ares={$cares_prefix} \
            --with-brotli={$brotli_prefix} \
            --with-default-ssl-backend=openssl \
            --with-nghttp2 \
            --without-ngtcp2 \
            --without-nghttp3
EOF
            )
            ->withPkgName('libcurl')
            ->withBinPath($curl_prefix . '/bin/')
            ->depends('openssl', 'cares', 'zlib', 'brotli', 'libzstd', 'libidn2', 'nghttp2')


        #--with-gnutls=GNUTLS_PREFIX
        #--with-nghttp3=NGHTTP3_PREFIX
        #--with-ngtcp2=NGTCP2_PREFIX
        #--with-nghttp2=NGHTTP2_PREFIX
        #--without-brotli
        #--disable-ares

        #--with-ngtcp2=/usr/ngtcp2 \
        #--with-quiche=/usr/quiche
        #--with-msh3=PATH
    );
    /**
     * configure: pkg-config: SSL_LIBS: "-lssl -lcrypto"
     * configure: pkg-config: SSL_LDFLAGS: "-L/usr/openssl/lib"
     * configure: pkg-config: SSL_CPPFLAGS: "-I/usr/openssl/include"
     *
     * onfigure: pkg-config: IDN_LIBS: "-lidn2"
     * configure: pkg-config: IDN_LDFLAGS: "-L/usr/libidn2/lib"
     * configure: pkg-config: IDN_CPPFLAGS: "-I/usr/libidn2/include"
     * configure: pkg-config: IDN_DIR: "/usr/libidn2/lib"
     *
     * configure: -l is -lnghttp2
     * configure: -I is -I/usr/nghttp2/include
     * configure: -L is -L/usr/nghttp2/lib
     * # search idn2_lookup_ul
     *
     * configure: pkg-config: ares LIBS: "-lcares"
     * configure: pkg-config: ares LDFLAGS: "-L/usr/cares/lib"
     * configure: pkg-config: ares CPPFLAGS: "-I/usr/cares/include"
     * -lidn -lrt
     */
}


function install_pgsql(Preprocessor $p): void
{
    $pgsql_prefix = PGSQL_PREFIX;

    $openssl_prefix = OPENSSL_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $libxslt_prefix = LIBXSLT_PREFIX;
    $readline_prefix = READLINE_PREFIX;
    $icu_prefix = ICU_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;


    $includes = <<<EOF
{$openssl_prefix}/include/:
{$libxml2_prefix}/include/:
{$libxslt_prefix}/include:
{$readline_prefix}/include/readline:
{$icu_prefix}/include:
{$zlib_prefix}/include:
/usr/include

EOF;

    $includes = trim(str_replace(PHP_EOL, '', $includes));
    $libraries = <<<EOF
{$openssl_prefix}/lib/:
{$libxml2_prefix}/lib/:
{$libxslt_prefix}/lib:
{$readline_prefix}/lib:
{$icu_prefix}/lib:
{$zlib_prefix}/lib:
/usr/lib
EOF;

    $libraries = trim(str_replace(PHP_EOL, '', $libraries));

    $link_cpp = $p->getOsType() == 'macos' ? '-lc++' : '-lstdc++';
    $p->addLibrary(
        (new Library('pgsql'))
            ->withHomePage('https://www.postgresql.org/')
            ->withLicense('https://www.postgresql.org/about/licence/', Library::LICENSE_SPEC)
            ->withUrl('https://ftp.postgresql.org/pub/source/v15.1/postgresql-15.1.tar.gz')
            //https://www.postgresql.org/docs/devel/installation.html
            //https://www.postgresql.org/docs/devel/install-make.html#INSTALL-PROCEDURE-MAKE
            ->withManual('https://www.postgresql.org/docs/')
            ->withPrefix($pgsql_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($pgsql_prefix)
            ->withBuildScript(
                <<<'EOF'

            sed -i.backup "s/invokes exit\'; exit 1;/invokes exit\';/"  src/interfaces/libpq/Makefile

            # 替换指定行内容
            sed -i.backup "102c all: all-lib" src/interfaces/libpq/Makefile

            # export CFLAGS="-static -g -fPIE -fPIC -O2 -Wall "
            ./configure --help

            # --with-includes="{$includes}"
            # --with-libraries="{$libraries}"
            package_names="icu-uc icu-io icu-i18n readline libxml-2.0 openssl zlib libxslt liblz4 libzstd"

            CPPFLAGS="$(pkg-config  --cflags-only-I --static $package_names )" \
            LDFLAGS="$(pkg-config   --libs-only-L   --static $package_names )" \
EOF
                . PHP_EOL . <<<EOF
            LIBS="\$(pkg-config      --libs-only-l   --static \$package_names ) $link_cpp" \
            ./configure  --prefix={$pgsql_prefix} \
            --enable-coverage=no \
            --with-ssl=openssl  \
            --with-readline \
            --with-icu \
            --without-ldap \
            --with-libxml  \
            --with-libxslt \
            --with-lz4 \
            --with-zstd \
            --without-python \
            --without-perl \
            --without-systemd



EOF
                . PHP_EOL . <<<'EOF'
            result_code=$?
            [[ $result_code -ne 0 ]] && echo "[make FAILURE]" && exit $result_code;

            # make -j $cpu_nums
            # make -C  src/bin/pg_config install
            # make install



            make -C src/include install

            make -C  src/bin/pg_config install

            make -C  src/common -j $cpu_nums all
            make -C  src/common install


            make -C  src/port -j $cpu_nums all
            make -C  src/port install


            make -C  src/backend/libpq -j $cpu_nums all
            make -C  src/backend/libpq install


            make -C src/interfaces/ecpg   -j $cpu_nums all-pgtypeslib-recurse all-ecpglib-recurse all-compatlib-recurse all-preproc-recurse
            make -C src/interfaces/ecpg  install-pgtypeslib-recurse install-ecpglib-recurse install-compatlib-recurse install-preproc-recurse


            # 静态编译 src/interfaces/libpq/Makefile  有静态配置  参考： all-static-lib

            make -C src/interfaces/libpq  -j $cpu_nums # soname=true
            make -C src/interfaces/libpq  install

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$pgsql_prefix}/lib/*.so.*
            rm -rf {$pgsql_prefix}/lib/*.so
            rm -rf {$pgsql_prefix}/lib/*.dylib
EOF
            )
            ->withPkgName('libpq')
            ->withBinPath($pgsql_prefix . '/bin/')
    );
}


function install_libffi($p): void
{
    $libffi_prefix = LIBFFI_PREFIX;
    $p->addLibrary(
        (new Library('libffi'))
            ->withHomePage('https://sourceware.org/libffi/')
            ->withLicense('http://github.com/libffi/libffi/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/libffi/libffi/releases/download/v3.4.4/libffi-3.4.4.tar.gz')
            ->withFile('libffi-3.4.4.tar.gz')
            ->withPrefix($libffi_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libffi_prefix)
            ->withConfigure(
                "
            ./configure --help ;
            ./configure \
            --prefix={$libffi_prefix} \
            --enable-shared=no \
            --enable-static=yes
            "
            )
            ->withPkgName('libffi')
            ->withBinPath($libffi_prefix . '/bin/')
    );
}

function install_bison(Preprocessor $p)
{
    $bison_prefix = BISON_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $readline_prefix = READLINE_PREFIX;
    $p->addLibrary(
        (new Library('bison'))
            ->withHomePage('https://www.gnu.org/software/bison/')
            ->withUrl('http://ftp.gnu.org/gnu/bison/bison-3.8.tar.gz')
            ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
            ->withManual('https://www.gnu.org/software/bison/manual/')
            ->withLabel('build_env_bin')
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($bison_prefix)
            ->withScriptBeforeConfigure(
                <<<'EOF'
            export PATH=$SYSTEM_ORIGIN_PATH
            export PKG_CONFIG_PATH=$SYSTEM_ORIGIN_PKG_CONFIG_PATH
EOF
            )
            ->withConfigure(
                "
             ./configure --help

             ./configure --prefix={$bison_prefix} \
             --enable-silent-rules \
             --disable-dependency-tracking
            "
            )
            ->withScriptAfterInstall(
                <<<'EOF'
            export PATH=$SWOOLE_CLI_PATH
            export PKG_CONFIG_PATH=$SWOOLE_CLI_PKG_CONFIG_PATH
EOF
            )
            ->withBinPath($bison_prefix . '/bin/')
            ->withPkgName('bision')
    );
}

function install_re2c(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('re2c'))
            ->withHomePage('http://re2c.org/')
            ->withUrl('https://github.com/skvadrik/re2c/releases/download/3.0/re2c-3.0.tar.xz')
            ->withLicense('https://github.com/skvadrik/re2c/blob/master/LICENSE', Library::LICENSE_GPL)
            ->withManual('https://re2c.org/build/build.html')
            ->withLabel('build_env_bin')
            ->withCleanBuildDirectory()
            ->withScriptBeforeConfigure(
                '
             autoreconf -i -W all
            '
            )
            ->withConfigure(
                "
             ./configure --help
             ./configure --prefix=/usr/re2c
            "
            )
            ->withBinPath('/usr/re2c/bin/')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}

function install_libmcrypt(Preprocessor $p)
{
    $libmcrypt_prefix = LIBMCRYPT_PREFIX;
    $lib = new Library('libmcrypt');
    $lib->withHomePage('https://sourceforge.net/projects/mcrypt/files/Libmcrypt/')
        ->withLicense('https://gitlab.com/libtiff/libtiff/-/blob/master/LICENSE.md', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/winlibs/libmcrypt/archive/refs/tags/libmcrypt-2.5.8-3.4.tar.gz')
        ->withManual('https://github.com/winlibs/libmcrypt/blob/master/INSTALL')
        ->withPrefix($libmcrypt_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libmcrypt_prefix)
        ->withConfigure(
            <<<EOF
sh ./configure --help
chmod a+x ./install-sh
sh ./configure --prefix=$libmcrypt_prefix \
--enable-static=yes \
--enable-shared=no


EOF
        )
        ->withPkgName('libmcrypt');

    $p->addLibrary($lib);
}

function install_libxlsxwriter(Preprocessor $p)
{
    $libxlsxwriter_prefix = LIBXLSXWRITER_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $lib = new Library('libxlsxwriter');
    $lib->withHomePage('https://libxlsxwriter.github.io/')
        ->withLicense('https://github.com/jmcnamara/libxlsxwriter/blob/main/License.txt', Library::LICENSE_BSD)
        ->withLicense('https://libxlsxwriter.github.io/license.html', Library::LICENSE_BSD)
        ->withUrl('https://github.com/jmcnamara/libxlsxwriter/archive/refs/tags/RELEASE_1.1.5.tar.gz')
        ->withFile('libxlsxwriter-1.1.5.tar.gz')
        ->withManual('http://libxlsxwriter.github.io/getting_started.html')
        ->withPrefix($libxlsxwriter_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libxlsxwriter_prefix)
        ->withBuildScript(
            <<<EOF
            # 启用DBUILD_TESTS 需要安装python3 pytest
            mkdir -p build
            cd build
            cmake .. -DCMAKE_INSTALL_PREFIX={$libxlsxwriter_prefix} \
            -DCMAKE_BUILD_TYPE=Release \
            -DZLIB_ROOT={$zlib_prefix} \
            -DBUILD_TESTS=OFF \
            -DBUILD_EXAMPLES=OFF \
            -DUSE_STANDARD_TMPFILE=ON \
            -DUSE_OPENSSL_MD5=ON

            cmake --build . --config Release --target install
EOF
        )
        ->depends('zlib')
        ->withPkgName('xlsxwriter');

    $p->addLibrary($lib);
}

function install_minizip(Preprocessor $p)
{
    $libminzip_prefix = LIBMINZIP_PREFIX;
    $libzip2_prefix = BZIP2_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $lib = new Library('libminizip');
    $lib->withHomePage('https://github.com/zlib-ng/minizip-ng')
        ->withLicense('https://github.com/zlib-ng/minizip-ng/blob/master/LICENSE', Library::LICENSE_SPEC)
        ->withUrl('https://github.com/zlib-ng/minizip-ng/archive/refs/tags/3.0.9.tar.gz')
        ->withFile('minizip-ng-3.0.9.tar.gz')
        ->withManual('https://github.com/zlib-ng/minizip-ng')
        ->withPrefix($libminzip_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libminzip_prefix)
        ->withBuildScript(
            <<<EOF
            # -Wno-dev 

            cmake   -S . -B build \
            -D CMAKE_INSTALL_PREFIX={$libminzip_prefix} \
            -D MZ_ZLIB=ON \
            -D MZ_BZIP2=ON \
            -D MZ_LZMA=ON \
            -D MZ_ZSTD=ON \
            -D MZ_OPENSSL=ON \
            -D MZ_COMPAT=ON \
            -D MZ_ICONV=ON \
            -D MZ_FETCH_LIBS=OFF \
            -D MZ_BUILD_TESTS=ON \
            -D ZLIB_ROOT={$zlib_prefix} \
            -D BZIP2_ROOT={$libzip2_prefix} \

            cmake --build build  --config Release --target install
EOF
        )
        ->depends('zlib', 'bzip2', 'liblzma', 'libzstd', 'openssl', 'libiconv')
        ->withBinPath($libminzip_prefix . '/bin/')
        ->withPkgName('minizip');

    $p->addLibrary($lib);
}

function install_libxlsxio(Preprocessor $p)
{
    $libxlsxio_prefix = LIBXLSXIO_PREFIX;
    $libminizip_prefix = LIBMINZIP_PREFIX;
    $libzip_prefix = ZIP_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $libexpat_prefix = LIBEXPAT_PREFIX;
    $lib = new Library('libxlsxio');
    $lib->withHomePage('https://github.com/brechtsanders/xlsxio.git')
        ->withLicense('https://github.com/brechtsanders/xlsxio/blob/master/LICENSE.txt', Library::LICENSE_MIT)
        ->withUrl('https://github.com/brechtsanders/xlsxio/archive/refs/tags/0.2.34.tar.gz')
        ->withFile('libxlsxio-0.2.34.tar.gz')
        ->withManual('https://brechtsanders.github.io/xlsxio/')
        ->withPrefix($libxlsxio_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libxlsxio_prefix)
        ->withConfigure(
            <<<EOF
            # apk add graphviz  doxygen
            # export CFLAGS="$(pkg-config  --cflags --static expat minizip ) " 
          
            cmake -G"Unix Makefiles" .  \
            -DCMAKE_INSTALL_PREFIX={$libxlsxio_prefix} \
            -DBUILD_STATIC=ON \
            -DBUILD_SHARED=OFF \
            -DBUILD_TOOLS=OFF \
            -DBUILD_EXAMPLES=OFF \
            -DWITH_WIDE=ON \
            -DMINIZIP_DIR={$libminizip_prefix} \
            -DZLIB_DIR={$zlib_prefix} \
            -DWITH_LIBZIP=ON \
            -DLIBZIP_DIR={$libzip_prefix} \
            -DEXPAT_DIR={$libexpat_prefix} \
            -DBUILD_DOCUMENTATION=OFF



EOF
        )
        ->depends('zlib', 'libzip')
        ->withPkgName('libxlsxio');

    $p->addLibrary($lib);
}


function install_libevent($p)
{
    $libevent_prefix = LIBEVENT_PREFIX;
    $p->addLibrary(
        (new Library('libevent'))
            ->withHomePage('https://github.com/libevent/libevent')
            ->withLicense('https://github.com/libevent/libevent/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl(
                'https://github.com/libevent/libevent/releases/download/release-2.1.12-stable/libevent-2.1.12-stable.tar.gz'
            )
            ->withManual('https://libevent.org/libevent-book/')
            ->withPrefix($libevent_prefix)
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            # 查看更多选项
            # cmake -LAH .
        mkdir -p build
        cd build
        cmake ..   \
        -DCMAKE_INSTALL_PREFIX={$libevent_prefix} \
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
    $libuv_prefix = LIBUV_PREFIX;
    $p->addLibrary(
        (new Library('libuv'))
            ->withHomePage('https://libuv.org/')
            ->withLicense('https://github.com/libuv/libuv/blob/v1.x/LICENSE', Library::LICENSE_MIT)
            ->withUrl('https://github.com/libuv/libuv/archive/refs/tags/v1.44.2.tar.gz')
            ->withManual('https://github.com/libuv/libuv.git')
            ->withFile('libuv-v1.44.2.tar.gz')
            ->withPrefix($libuv_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libuv_prefix)
            ->withConfigure(
                <<<EOF
            ls -lh

            sh autogen.sh
            ./configure --help

            ./configure --prefix={$libuv_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
            )
            ->withPkgName('libuv')
    );
}
