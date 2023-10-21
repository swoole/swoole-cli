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
    //install_openssl_v1($p);
    install_openssl_v3_quic($p);
}

function install_openssl_v1(Preprocessor $p)
{

}

function install_openssl_v3(Preprocessor $p)
{

}

function install_openssl_v3_quic(Preprocessor $p)
{

}

function install_libiconv(Preprocessor $p): void
{

}


// Dependent libiconv
function install_libxml2(Preprocessor $p)
{


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

}

function install_cares(Preprocessor $p)
{
}


function install_gmp(Preprocessor $p)
{
}


/*
// CFLAGS="-static -O2 -Wall" \
// LDFLAGS="-Wl,R-lncurses"
// LDFLAGS="-lncurses" \
 */
function install_ncurses(Preprocessor $p)
{
}


function install_readline(Preprocessor $p)
{
}


function install_libyaml(Preprocessor $p): void
{
}

function install_libsodium(Preprocessor $p)
{

}

function install_bzip2(Preprocessor $p)
{

}

function install_zlib(Preprocessor $p)
{

}


function install_liblz4(Preprocessor $p)
{

    //可以使用CMAKE 编译 也可以
    //不使用CMAKE，需要自己修改安装目录
    //->withMakeOptions('INSTALL_PROGRAM=/usr/liblz4/')
    //->withMakeInstallOptions("DESTDIR=/usr/liblz4/")
}


function install_liblzma(Preprocessor $p)
{

}


function install_libzstd(Preprocessor $p)
{
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

}


function install_sqlite3(Preprocessor $p)
{

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
    //https://github.com/unicode-org/icu/

}

function install_oniguruma(Preprocessor $p)
{

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

}

function install_libssh2(Preprocessor $p)
{

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
    // curl 依赖库 https://curl.se/docs/libs.html

    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;

    $libidn2_prefix = LIBIDN2_PREFIX;
    $libzstd_prefix = LIBZSTD_PREFIX;
    $cares_prefix = CARES_PREFIX;
    $brotli_prefix = BROTLI_PREFIX;
    $gnutls_prefix = GNUTLS_PREFIX;
    $libssh2_prefix = LIBSSH2_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $cares_prefix = CARES_PREFIX;

    $curl_prefix = CURL_PREFIX;
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
            ./configure --help

            PACKAGES='zlib openssl libcares libbrotlicommon libbrotlidec libbrotlienc libzstd libnghttp2 '
            PACKAGES="\$PACKAGES libidn2 libssh2 " #libnghttp3 libngtcp2  libngtcp2_crypto_openssl
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES)" \
            LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES)" \
            LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES)" \
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
            --enable-ares={$cares_prefix} \
            --with-nghttp2 \
            --with-ngtcp2 \
            --with-nghttp3 \
            --with-libidn2 \
            --with-libssh2 \
            --with-openssl  \
            --with-default-ssl-backend=openssl \
            --without-gnutls \
            --without-mbedtls \
            --without-wolfssl \
            --without-bearssl \
            --without-rustls
EOF
            )
            ->withPkgName('libcurl')
            ->withBinPath($curl_prefix . '/bin/')
            ->withDependentLibraries(
                'openssl',
                'cares',
                'zlib',
                'brotli',
                'libzstd',
                'libidn2',
                'nghttp2',
                // 'nghttp3',
                //'ngtcp2',
                'libssh2'
            )
    );

    #--with-gnutls=GNUTLS_PREFIX
    #--with-nghttp3=NGHTTP3_PREFIX
    #--with-ngtcp2=NGTCP2_PREFIX
    #--with-nghttp2=NGHTTP2_PREFIX
    #--without-brotli
    #--disable-ares
    #--with-ngtcp2=/usr/ngtcp2 \
    #--with-quiche=/usr/quiche
    #--with-msh3=PATH
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
            ->withConfigure(
                "
              autoreconf -i -W all
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
            cmake .. \
            -DCMAKE_INSTALL_PREFIX={$libxlsxwriter_prefix} \
            -DCMAKE_BUILD_TYPE=Release \
            -DBUILD_SHARED_LIBS=OFF \
            -DZLIB_ROOT={$zlib_prefix} \
            -DBUILD_TESTS=OFF \
            -DBUILD_EXAMPLES=OFF \
            -DUSE_DTOA_LIBRARY=ON \
            -DUSE_OPENSSL_MD5=OFF \
            -DUSE_NO_MD5=OFF \
            -DUSE_SYSTEM_MINIZIP=OFF \
            -DUSE_STANDARD_TMPFILE=OFF

            cmake --build . --config Release --target install
EOF
        )
        ->depends('zlib', 'openssl')
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
            -DCMAKE_INSTALL_PREFIX={$libminzip_prefix} \
            -DCMAKE_INSTALL_LIBDIR={$libminzip_prefix}/lib \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DMZ_ZLIB=ON \
            -DMZ_BZIP2=ON \
            -DMZ_LZMA=ON \
            -DMZ_ZSTD=ON \
            -DMZ_OPENSSL=ON \
            -DMZ_COMPAT=ON \
            -DMZ_ICONV=ON \
            -DMZ_FETCH_LIBS=OFF \
            -DMZ_FORCE_FETCH_LIBS=OFF \
            -DMZ_BUILD_TESTS=ON \
            -DZLIB_ROOT={$zlib_prefix}  \
            -DBZIP2_ROOT={$libzip2_prefix}


            cmake --build build  --config Release --target install
            # mkdir -p {$libzip2_prefix}/include/minizip
            # cp -f {$libzip2_prefix}/include/*.h {$libzip2_prefix}/include/minizip
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

    $p->addLibrary(
        (new Library('libxlsxio'))
            ->withHomePage('https://github.com/brechtsanders/xlsxio.git')
            ->withLicense('https://github.com/brechtsanders/xlsxio/blob/master/LICENSE.txt', Library::LICENSE_MIT)
            ->withUrl('https://github.com/brechtsanders/xlsxio/archive/refs/tags/0.2.34.tar.gz')
            ->withFile('libxlsxio-0.2.34.tar.gz')
            ->withManual('https://brechtsanders.github.io/xlsxio/')
            ->withPrefix($libxlsxio_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libxlsxio_prefix)
            ->withConfigure(
                <<<EOF
            # apk add graphviz  doxygen  // 能看到常见安装的依赖库

            # export CFLAGS="$(pkg-config  --cflags --static expat minizip ) "
            #  SET (CMAKE_EXE_LINKER_FLAGS "-static")

            # find_package的简单用法   https://blog.csdn.net/weixin_43940314/article/details/128252940

            # CMAKE_BUILD_TYPE =  Debug Release

            cmake -G"Unix Makefiles" .  \
            -DCMAKE_INSTALL_PREFIX={$libxlsxio_prefix} \
            -DCMAKE_BUILD_TYPE=Release  \
            -DBUILD_SHARED_LIBS=OFF  \
            -DBUILD_SHARED=OFF \
            -DBUILD_STATIC=ON \
            -DBUILD_TOOLS=OFF \
            -DBUILD_EXAMPLES=OFF \
            -DBUILD_DOCUMENTATION=OFF \
            -DWITH_WIDE=ON \
            -DZLIB_DIR={$zlib_prefix} \
            -DZLIB_ROOT={$zlib_prefix} \
            -DEXPATW_DIR={$libexpat_prefix} \
            -DEXPATW_ROOT={$libexpat_prefix} \
            -DEXPATW_LIBRARIES={$libexpat_prefix} \
            -DWITH_LIBZIP=ON \
            -DLIBZIP_DIR={$libzip_prefix} \
            -DLIBZIP_ROOT={$libzip_prefix} \
            -DLIBZIP_LIBRARIES={$libzip_prefix}/lib \


            # -DMINIZIP_DIR={$libminizip_prefix} \
            # -DMINIZIP_LIBRARIES={$libminizip_prefix}/lib \
            # -DMINIZIP_INCLUDE_DIRS='{$libminizip_prefix}/include/' \



EOF
            )
            ->depends('zlib', 'libzip')
            ->withPkgName('libxlsxio_read')
            ->withPkgName('libxlsxio_readw')
            ->withPkgName('libxlsxio_write')
    );
}


function install_libevent($p)
{

}
