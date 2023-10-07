<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

/*
            ZIP_CFLAGS=$(pkg-config --cflags libzip) ;
            ZIP_LIBS=$(pkg-config --libs libzip) ;
            ZLIB_CFLAGS=$(pkg-config --cflags zlib) ;
            ZLIB_LIBS=$(pkg-config --libs zlib) ;
            LIBZSTD_CFLAGS=$(pkg-config --cflags libzstd) ;
            LIBZSTD_LIBS=$(pkg-config --libs libzstd) ;
            FREETYPE_CFLAGS=$(pkg-config --cflags freetype2) ;
            FREETYPE_LIBS=$(pkg-config --libs freetype2) ;
            LZMA_CFLAGS=$(pkg-config --cflags liblzma) ;
            LZMA_LIBS=$(pkg-config --libs liblzma) ;
            PNG_CFLAGS=$(pkg-config --cflags libpng  libpng16) ;
            PNG_LIBS=$(pkg-config --libs libpng  libpng16) ;
            WEBP_CFLAGS=$(pkg-config --cflags libwebp ) ;
            WEBP_LIBS=$(pkg-config --libs libwebp ) ;
            WEBPMUX_CFLAGS=$(pkg-config --cflags libwebp libwebpdemux  libwebpmux) ;
            WEBPMUX_LIBS=$(pkg-config --libs libwebp libwebpdemux  libwebpmux) ;
            XML_CFLAGS=$(pkg-config --cflags libxml-2.0) ;
            XML_LIBS=$(pkg-config --libs libxml-2.0) ;
 */


function install_libgcrypt_error(Preprocessor $p)
{
    $libgcrypt_error_prefix = LIBGCRYPT_ERROR_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libgcrypt_error'))
            ->withHomePage('https://www.gnupg.org/')
            ->withUrl('https://www.gnupg.org/ftp/gcrypt/libgpg-error/libgpg-error-1.46.tar.bz2')
            ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
            ->withManual('https://www.gnupg.org/documentation/manuals.html')
            ->withPrefix($libgcrypt_error_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libgcrypt_error_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libgcrypt_error_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --with-libiconv-prefix={$libiconv_prefix} \
            --with-libintl-prefix  \
            --disable-doc \
            --disable-tests

EOF
            )
            ->withBinPath($libgcrypt_error_prefix . '/bin')
            ->withPkgName('gpg-error')
    );
}

function install_libgcrypt(Preprocessor $p)
{
    $libgcrypt_prefix = LIBGCRYPT_PREFIX;
    $p->addLibrary(
        (new Library('libgcrypt'))
            ->withHomePage('https://www.gnupg.org/')
            ->withUrl('https://www.gnupg.org/ftp/gcrypt/libgcrypt/libgcrypt-1.10.1.tar.bz2')
            ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
            ->withManual('https://www.gnupg.org/documentation/manuals.html')
            ->withPrefix($libgcrypt_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libgcrypt_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libgcrypt_prefix} \
            --enable-static=yes \
            --enable-shared=no \

EOF
            )
            ->withPkgName('libgcrypt')
            ->withBinPath($libgcrypt_prefix . '/bin/')
    );
}

/**
 * libgcrypt是一个非常成熟的加密算法库，也是著名的开源加密软件GnuPG的底层库，支持多种对称、非对称加密算法，以及多种Hash算法。
 * @param Preprocessor $p
 * @return void
 */
function install_gnupg(Preprocessor $p)
{
    $gnupg_prefix = GNUPG_PREFIX;
    $p->addLibrary(
        (new Library('gnupg'))
            ->withHomePage('https://www.gnupg.org/')
            ->withUrl('https://www.gnupg.org/ftp/gcrypt/gnupg/gnupg-2.4.0.tar.bz2')
            ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
            ->withManual('https://www.gnupg.org/documentation/manuals.html')
            ->withPrefix($gnupg_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($gnupg_prefix)
            ->withBuildScript(
                <<<EOF
            ./configure --help
EOF
            )
            ->withPkgName('gnupg')
    );
}

function install_libyuv(Preprocessor $p)
{

}


function install_libraw(Preprocessor $p)
{

}

function install_dav1d(Preprocessor $p)
{

}

function install_libgav1(Preprocessor $p)
{

}

function install_libavif(Preprocessor $p): void
{

}

function install_nasm(Preprocessor $p)
{
    $nasm_prefix = NASM_PREFIX;
    $p->addLibrary(
        (new Library('nasm'))
            ->withHomePage('https://www.nasm.us/')
            ->withUrl('https://www.nasm.us/pub/nasm/releasebuilds/2.16.01/nasm-2.16.01.tar.gz')
            ->withLicense('http://opensource.org/licenses/BSD-2-Clause', Library::LICENSE_BSD)
            ->withManual('https://github.com/netwide-assembler/nasm.git')
            ->withMd5sum('42c4948349d01662811c8641fad4494c')
            ->withDownloadWithOriginURL()
            ->withPrefix($nasm_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($nasm_prefix)
            ->withConfigure(
                <<<EOF
                sh autogen.sh
                sh configure --help
                sh configure --prefix={$nasm_prefix}

EOF
            )
            ->withPkgName('')
            ->withLdflags('')
            ->withBinPath($nasm_prefix . '/bin/')
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
        --enable-static=yes \
        --with-pic
EOF
        )
        ->withPkgName('libde265');

    $p->addLibrary($lib);
}

function install_svt_av1(Preprocessor $p)
{

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
            <<<EOF
        mkdir -p build
        cd build
        cmake .. -G"Unix Makefiles" \
        -DCMAKE_INSTALL_PREFIX={$libheif_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON \
        -DWITH_EXAMPLES=OFF
EOF
        )
        ->withPkgName('libheif');

    $p->addLibrary($lib);
}


function install_graphite2(Preprocessor $p)
{
    $graphite2_prefix = "/usr/graphite2";
    $p->addLibrary(
        (new Library('graphite2'))
            ->withLicense('https://github.com/silnrsi/graphite/blob/master/COPYING', Library::LICENSE_SPEC)
            ->withHomePage('http://graphite.sil.org/')
            ->withUrl('https://github.com/silnrsi/graphite/archive/refs/tags/1.3.14.tar.gz')
            ->withManual('https://github.com/silnrsi/graphite.git')
            ->withFile('graphite-1.3.14.tar.gz')
            ->withLabel('library')
            ->withPrefix($graphite2_prefix)
            ->withCleanBuildDirectory()
            ->withConfigure(
                "
            mkdir -p build
            cd build
            cmake   ..  \
            -DCMAKE_INSTALL_PREFIX={$graphite2_prefix} \
            -DCMAKE_BUILD_TYPE=Release \
            -DBUILD_SHARED_LIBS=OFF
            "
            )
            ->withPkgName('graphite2')
    );
}

function install_libfribidi(Preprocessor $p)
{

}

function install_harfbuzz(Preprocessor $p)
{

}

function install_libgd2($p)
{
    $libgd_prefix = LIBGD_PREFIX;
    $libiconv_prefix = ICONV_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $webp_prefix = WEBP_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
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
            <<<EOF
        mkdir -p build
        cd build
        cmake   ..  \
        -DCMAKE_INSTALL_PREFIX={$libgd_prefix} \
        -DCMAKE_BUILD_TYPE=Release \
        -DENABLE_GD_FORMATS=1 \
        -DENABLE_JPEG=1 \
        -DENABLE_TIFF=1 \
        -DENABLE_ICONV=1 \
        -DENABLE_FREETYPE=0 \
        -DENABLE_FONTCONFIG=0 \
        -DENABLE_WEBP=1 \
        -DENABLE_HEIF=1 \
        -DENABLE_AVIF=1 \
        -DENABLE_WEBP=1 \
        -DZLIB_ROOT={$zlib_prefix} \
        -DWEBP_ROOT={$webp_prefix} \
        -DICONV_ROOT={$iconv_prefix}

        cmake --build . -- -j$(nproc)
        cmake --install .



:<<'_EOF_'



        ./configure --help
        # freetype2 libbrotlicommon libbrotlidec  libbrotlienc
        PACKAGES='zlib libpng  libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux libtiff-4 libavif '
        CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES ) " \
        LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES ) " \
        LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES ) " \
        ./configure \
        --prefix={$libgd_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --with-libiconv-prefix={$libiconv_prefix} \
        --without-freetype
        # --with-freetype=/usr/freetype \
        # --without-freetype


_EOF_

EOF
        )
        ->withMakeInstallCommand('')
        ->withPkgName('libgd2');

    $p->addLibrary($lib);
}

function install_librsvg($p)
{
    $librsvg_prefix = LIBRSVG_PREFIX;

    $lib = new Library('librsvg');
    $lib->withHomePage('https://gitlab.gnome.org/GNOME/librsvg')
        ->withLicense('https://gitlab.gnome.org/GNOME/librsvg/-/blob/main/COPYING.LIB', Library::LICENSE_LGPL)
        ->withUrl('https://gitlab.gnome.org/GNOME/librsvg')
        ->withManual('https://gitlab.gnome.org/GNOME/librsvg')
        ->withFile('librsvg-v2.56.0')
        ->withDownloadScript(
            'librsvg',
            <<<EOF
            git clone -b 2.56.0 --depth=1 https://gitlab.gnome.org/GNOME/librsvg.git
EOF
        )
        ->withPrefix($librsvg_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($librsvg_prefix)
        ->withConfigure(
            <<<EOF
            ./configure \
            --prefix=$librsvg_prefix
EOF
        )
        ->withPkgName('librsvg');

    $p->addLibrary($lib);
}

function install_GraphicsMagick($p)
{
    $libiconv_prefix = ICONV_PREFIX;
    $GraphicsMagick_prefix = '/usr/GraphicsMagick';
    $lib = new Library('GraphicsMagick');
    $lib->withHomePage('http://www.graphicsmagick.org/index.html')
        ->withLicense('https://github.com/libgd/libgd/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withUrl(
            'https://jaist.dl.sourceforge.net/project/graphicsmagick/graphicsmagick/1.3.40/GraphicsMagick-1.3.40.tar.gz'
        )
        ->withManual('http://www.graphicsmagick.org/README.html')
        ->withManual('http://www.graphicsmagick.org/INSTALL-unix.html')
        ->withPrefix($GraphicsMagick_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($GraphicsMagick_prefix)
        ->withConfigure(
            <<<'EOF'
        # 下载依赖
         ./configure --help
         # -lbrotlicommon-static -lbrotlidec-static -lbrotlienc-static
        export CPPFLAGS="$(pkg-config  --cflags-only-I  --static zlib libpng freetype2 libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux  libbrotlicommon  libbrotlidec  libbrotlienc ) " \
        export LDFLAGS="$(pkg-config   --libs-only-L    --static zlib libpng freetype2 libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux  libbrotlicommon  libbrotlidec  libbrotlienc ) " \
        export LIBS="$(pkg-config      --libs-only-l    --static zlib libpng freetype2 libjpeg  libturbojpeg libwebp  libwebpdecoder  libwebpdemux  libwebpmux  libbrotlicommon  libbrotlidec  libbrotlienc ) " \

        echo $LIBS

EOF. PHP_EOL . <<<EOF
        ./configure \
        --prefix={$GraphicsMagick_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --without-freetype \
        --with-libiconv-prefix={$libiconv_prefix}
         # --with-freetype=/usr/freetype \

EOF
        )
        ->withMakeInstallCommand('')
        ->withPkgName('GraphicsMagick');

    $p->addLibrary($lib);
}


function install_libXpm(Preprocessor $p)
{
    $libXpm_prefix = LIBXPM_PREFIX;
    $lib = new Library('libXpm');
    $lib->withHomePage('https://github.com/freedesktop/libXpm.git')
        ->withLicense('https://github.com/freedesktop/libXpm/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withUrl('https://github.com/freedesktop/libXpm/archive/refs/tags/libXpm-3.5.11.tar.gz')
        ->withFile('libXpm-3.5.11.tar.gz')
        ->withPrefix($libXpm_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libXpm_prefix)
        ->withConfigure(
            <<<EOF

         # 解决依赖
         apk add util-macros xorgproto libx11

            ./autogen.sh
            ./configure --help
            ./configure \
            --prefix={$libXpm_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
        )
        ->withPkgName('libXpm');

    $p->addLibrary($lib);
}


function install_libOpenEXR(Preprocessor $p)
{

}

/**
 * @param Preprocessor $p
 * @return void
 * 并发编程：SIMD 介绍  https://zhuanlan.zhihu.com/p/416172020
 */
function install_highway(Preprocessor $p)
{
    $highway_prefix = '/usr/highway';
    $lib = new Library('highway');
    $lib->withHomePage('https://github.com/google/highway.git')
        ->withLicense('https://github.com/google/highway/blob/master/LICENSE', Library::LICENSE_APACHE2)
        ->withUrl('https://github.com/google/highway/archive/refs/tags/1.0.3.tar.gz')
        ->withFile('highway-1.0.3.tar.gz')
        ->withManual('https://github.com/google/highway.git')
        ->withPrefix($highway_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($highway_prefix)
        ->withConfigure(
            <<<EOF
# -DHWY_CMAKE_ARM7:BOOL=ON

# 会自动下载 googletest


    mkdir -p build && cd build
    cmake .. \
    -DCMAKE_INSTALL_PREFIX={$highway_prefix} \
    -DCMAKE_BUILD_TYPE=Release  \
    -DBUILD_SHARED_LIBS=OFF \
    -DHWY_FORCE_STATIC_LIBS=ON \
    -DBUILD_TESTING=OFF

EOF
        )
        ->withPkgName('libhwy-contrib.pc  libhwy-test.pc  libhwy');

    $p->addLibrary($lib);
}

function install_libjxl(Preprocessor $p)
{

}


function install_libedit(Preprocessor $p)
{

}


function install_libdeflate(Preprocessor $p)
{

}


function install_bzip2_dev_latest(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('bzip2', '/usr/bzip2'))
            ->withUrl('https://gitlab.com/bzip2/bzip2/-/archive/master/bzip2-master.tar.gz')
            ->withCleanBuildDirectory()
            ->withConfigure(
                '
                    cmake .. -DCMAKE_BUILD_TYPE="Release" \
                    -DCMAKE_INSTALL_PREFIX=/usr/bzip2  \
                    -DENABLE_STATIC_LIB=ON ;
                    cmake --build . --target install   ;
                    cd - ;
                    :; #  shell空语句
                    pwd
                    return 0 ; # 返回本函数调用处，本函数后续代码不在执行
            '
            )
            ->withLdflags('-L/usr/bzip2/lib')
            ->withHomePage('https://www.sourceware.org/bzip2/')
            ->withLicense('https://www.sourceware.org/bzip2/', Library::LICENSE_BSD)
    );
}


function install_libev($p)
{
    $libev_prefix = LIBEV_PREFIX;
    $p->addLibrary(
        (new Library('libev'))
            ->withHomePage('http://software.schmorp.de/pkg/libev.html')
            ->withLicense('http://cvs.schmorp.de/libev/README', Library::LICENSE_BSD)
            ->withUrl('http://dist.schmorp.de/libev/libev-4.33.tar.gz')
            ->withManual('http://cvs.schmorp.de/libev/README')
            ->withPrefix($libev_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libev_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libev_prefix} \
            --enable-shared=no \
            --enable-static=yes
EOF
            )
    );


    $p->setVarable('SWOOLE_CLI_EXTRA_CPPLAGS', '$SWOOLE_CLI_EXTRA_CPPLAGS -I' . LIBEV_PREFIX . '/include');
    $p->setVarable('SWOOLE_CLI_EXTRA_LDLAGS', '$SWOOLE_CLI_EXTRA_LDLAGS -L' . LIBEV_PREFIX . '/lib');
    $p->setVarable('SWOOLE_CLI_EXTRA_LIBS', '$SWOOLE_CLI_EXTRA_LIBS -lev');
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
            ->withConfigure(
                <<<EOF
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
            ->withCleanPreInstallDirectory('/usr/unbound/')
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
    $note = <<<EOF

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

    $gnutls_prefix = GNUTLS_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $zlib_prefix = ZLIB_PREFIX;
    $p->addLibrary(
        (new Library('gnutls'))
            ->withHomePage('https://www.gnutls.org/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://www.gnupg.org/ftp/gcrypt/gnutls/v3.7/gnutls-3.7.8.tar.xz')
            ->withManual('https://gitlab.com/gnutls/gnutls.git')
            ->withManual('https://www.gnutls.org/download.html')
            ->withPrefix($gnutls_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($gnutls_prefix)
            ->withConfigure(
                <<<EOF

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
                export NETTLE_CFLAGS=$(pkg-config  --cflags --static nettle)
                export NETTLE_LIBS=$(pkg-config    --libs   --static nettle)
                export LIBIDN2_CFLAGS=$(pkg-config  --cflags --static libidn2)
                export LIBIDN2_LIBS=$(pkg-config    --libs   --static libidn2)

                # export P11_KIT_CFLAGS=$(pkg-config  --cflags --static p11-kit-1)
                # export P11_KIT_LIBS=$(pkg-config    --libs   --static p11-kit-1)



                export CPPFLAGS=$(pkg-config    --cflags   --static libbrotlicommon libbrotlienc libbrotlidec)
                export LIBS=$(pkg-config        --libs     --static libbrotlicommon libbrotlienc libbrotlidec)
                 //  exit 0
                # ./bootstrap
                ./configure --help | grep -e '--without'
                ./configure --help | grep -e '--with-'



                ./configure \
                --prefix={$gnutls_prefix} \
                --enable-static=yes \
                --enable-shared=no \
                --with-zstd \
                --with-brotli \
                --with-libiconv-prefix={$iconv_prefix} \
                --with-libz-prefix={$zlib_prefix} \
                --with-nettle-mini \
                --with-libintl-prefix \
                --with-included-unistring \
                --with-included-libtasn1 \
                --without-tpm2 \
                --without-tpm \
                --disable-doc \
                --disable-tests \
                --enable-openssl-compatibility \
                --without-p11-kit \
                --without-libseccomp-prefix \
                --without-libcrypto-prefix \
                --without-librt-prefix
                # --with-libev-prefix=/usr/libev \
EOF
            )->withPkgName('gnutls')
            ->withBinPath($gnutls_prefix . '/bin/')
    //依赖：nettle, hogweed, libtasn1, libidn2, p11-kit-1, zlib, libbrotlienc, libbrotlidec, libzstd -lgmp  -latomic
    );
}


function install_boringssl($p)
{
    $boringssl_prefix = BORINGSSL_PREFIX;
    $p->addLibrary(
        (new Library('boringssl'))
            ->withHomePage('https://boringssl.googlesource.com/boringssl/')
            ->withLicense(
                'https://boringssl.googlesource.com/boringssl/+/refs/heads/master/LICENSE',
                Library::LICENSE_BSD
            )
            ->withUrl('https://github.com/google/boringssl/archive/refs/heads/master.zip')
            ->withFile('boringssl-latest.tar.gz')
            ->withDownloadWithOriginURL()
            ->withDownloadScript(
                'boringssl',
                <<<EOF
            git clone -b master --depth=1 https://boringssl.googlesource.com/boringssl
EOF
            )
            ->withMirrorUrl('https://boringssl.googlesource.com/boringssl')
            ->withManual('https://boringssl.googlesource.com/boringssl/+/refs/heads/master/BUILDING.md')
            ->withPrefix($boringssl_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($boringssl_prefix)
            ->withBuildScript(
                <<<EOF

                mkdir -p build
                cd build
                cmake -GNinja .. \
                -DCMAKE_INSTALL_PREFIX=$boringssl_prefix
                -DCMAKE_BUILD_TYPE=Release \
                -DBUILD_SHARED_LIBS=OFF

                cd ..
                # ninja
                ninja -C build

                ninja -C build install
EOF
            )
            ->disableDefaultPkgConfig()
    //->withSkipBuildInstall()
    );
}

function install_wolfssl($p)
{
    $wolfssl_prefix = WOLFSSL_PREFIX;
    $p->addLibrary(
        (new Library('wolfssl'))
            ->withHomePage('https://github.com/wolfSSL/wolfssl.git')
            ->withLicense('https://github.com/wolfSSL/wolfssl/blob/master/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://github.com/wolfSSL/wolfssl/archive/refs/tags/v5.5.4-stable.tar.gz')
            ->withFile('wolfssl-v5.5.4-stable.tar.gz')
            ->withManual('https://wolfssl.com/wolfSSL/Docs.html')
            ->withPrefix($wolfssl_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($wolfssl_prefix)
            ->withBuildScript(
                <<<EOF
                ./autogen.sh
                ./configure --help

                ./configure  --prefix=/usr/wolfssl \
                --enable-static=yes \
                --enable-shared=no \
                --enable-all

EOF
            )
            ->withPkgName('wolfssl')
    //->withSkipBuildInstall()
    );
}

function install_libressl($p)
{
    $libressl_prefix = '/usr/libressl';
    $p->addLibrary(
        (new Library('libressl'))
            ->withHomePage('https://www.libressl.org/')
            ->withLicense('https://github.com/wolfSSL/wolfssl/blob/master/COPYING', Library::LICENSE_GPL)
            ->withUrl('https://ftp.openbsd.org/pub/OpenBSD/LibreSSL/libressl-3.5.4.tar.gz')
            ->withFile('libressl-3.5.4.tar.gz')
            ->withManual('https://github.com/libressl/portable.git')
            ->withCleanBuildDirectory()
            ->withPrefix($libressl_prefix)
            ->withCleanPreInstallDirectory($libressl_prefix)
            ->withConfigure(
                <<<EOF
            ./configure  --help
            ./configure \
            --prefix={$libressl_prefix} \
            --enable-shared=no \
            --enable-static=yes

EOF
            )
            ->withPkgName('libressl')
    //->withSkipBuildInstall()
    );
}

function install_nghttp3(Preprocessor $p)
{

}

function install_ngtcp2(Preprocessor $p)
{
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
            ->withBuildScript(
                '
             test  -d /usr/quiche && rm -rf /usr/quiche
             # export RUSTUP_DIST_SERVER=https://mirrors.tuna.edu.cn/rustup
             # export RUSTUP_UPDATE_ROOT=https://mirrors.tuna.edu.cn/rustup/rustup
             export http_proxy=http://192.168.3.26:8015
             export https_proxy=http://192.168.3.26:8015
             source /root/.cargo/env
             cp -rf /work/pool/lib/boringssl /work/thirdparty/quiche/
             export OPENSSL_DIR=/usr/openssl
             export OPENSSL_STATIC=Yes

            '
            )
            ->withConfigure(
                '
            cd quiche-master
            cargo build --help

            export QUICHE_BSSL_PATH=/work/thirdparty/quiche/boringssl
            cargo build --package quiche --release --features ffi,pkg-config-meta,qlog
            mkdir -p quiche/deps/boringssl/src/lib
            ln -vnf $(find target/release -name libcrypto.a -o -name libssl.a) quiche/deps/boringssl/src/lib/
            exit 0

            '
            )
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
            ->withUntarArchiveCommand('')
            ->withPrefix('/usr/msh3')
            ->withBuildScript(
                '
              cp -rf /work/pool/lib/msh3 /work/thirdparty/msh3
              apk add bsd-compat-headers
            '
            )
            ->withConfigure(
                <<<EOF
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

function install_nghttp2(Preprocessor $p): void
{

}


function install_libunistring($p)
{

}

function install_libintl(Preprocessor $p)
{

}

function install_gettext(Preprocessor $p)
{

}

function install_coreutils($p): void
{
    /*
        glibc是一个核心C运行时库.它提供了像printf(3)和的东西fopen(3).

        glib 是一个用C编写的基于对象的事件循环和实用程序库.

        gnulib 是一个库,提供从POSIX API到本机API的适配器.

        coreutils    包括常用的命令，如 cat、ls、rm、chmod、mkdir、wc、whoami 和许多其他命令

     */

}

function install_gnulib($p)
{
    /*
        glibc是一个核心C运行时库.它提供了像printf(3)和的东西fopen(3).

        glib 是一个用C编写的基于对象的事件循环和实用程序库.

        gnulib 是一个库,提供从POSIX API到本机API的适配器.

        gnulib，也称为GNU Portability Library，是 GNU 代码的集合，用于帮助编写可移植代码。

     */

}


function install_libunwind($p)
{

}


function install_jemalloc($p)
{


}

function install_tcmalloc($p)
{

}


function install_libelf(Preprocessor $p)
{

}

function install_libbpf(Preprocessor $p)
{

}

function install_capstone(Preprocessor $p)
{
    $capstone_prefix = CAPSTONE_PREFIX;
    $p->addLibrary(
        (new Library('capstone'))
            ->withHomePage('http://www.capstone-engine.org/')
            ->withLicense('https://github.com/capstone-engine/capstone/blob/master/LICENSE.TXT', Library::LICENSE_BSD)
            ->withUrl('https://github.com/capstone-engine/capstone/archive/refs/tags/4.0.2.tar.gz')
            ->withManual('http://www.capstone-engine.org/documentation.html')
            ->withPrefix($capstone_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($capstone_prefix)
            ->withConfigure(
                <<<EOF
             set -uex
             cmake . \
            -DCMAKE_INSTALL_PREFIX="{$capstone_prefix}" \
            -DCAPSTONE_BUILD_STATIC_RUNTIME=ON \
            -DCAPSTONE_BUILD_STATIC=ON \
            -DCAPSTONE_BUILD_SHARED=OFF \
            -DCAPSTONE_BUILD_TESTS=OFF  \
            -DCAPSTONE_BUILD_CSTOOL=ON

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
             rm -rf {$capstone_prefix}/lib/*.dylib
EOF
            )
            ->withPkgName('capstone')
            ->withBinPath($capstone_prefix . '/bin/')
    );
}

function install_dynasm(Preprocessor $p)
{
    $dynasm_prefix = DYNASM_PREFIX;
    $p->addLibrary(
        (new Library('dynasm'))
            ->withHomePage('https://luajit.org/dynasm.html')
            ->withLicense('https://www.opensource.org/licenses/mit-license.php', Library::LICENSE_MIT)
            ->withUrl('https://luajit.org/download/LuaJIT-2.0.5.tar.gz')
            ->withManual('https://luajit.org/download.html')
            ->withTutorial('https://corsix.github.io/dynasm-doc/tutorial.html')
            ->withPrefix($dynasm_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($dynasm_prefix)
            ->withMakeOptions('PREFIX=' . $dynasm_prefix)
            ->withMakeInstallOptions('PREFIX=' . $dynasm_prefix) //DESTDIR=/tmp/buildroot

            ->withPkgName('dynasm')
            ->withBinPath($dynasm_prefix . '/bin/')
    );
}

function install_valgrind(Preprocessor $p)
{
    $valgrind_prefix = VALGRIND_PREFIX;
    $p->addLibrary(
        (new Library('valgrind'))
            ->withHomePage('https://valgrind.org/')
            ->withLicense('http://www.gnu.org/licenses/gpl-2.0.html', Library::LICENSE_LGPL)
            ->withUrl('https://sourceware.org/pub/valgrind/valgrind-3.20.0.tar.bz2')
            ->withManual('https://valgrind.org/docs/man')
            ->withPrefix($valgrind_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($valgrind_prefix)
            ->withConfigure(
                <<<EOF
                export PATH=\$SYSTEM_ORIGIN_PATH
                export PKG_CONFIG_PATH=\$SYSTEM_ORIGIN_PKG_CONFIG_PATH

                ./autogen.sh
                ./configure \
                --prefix={$valgrind_prefix}

EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
                export PATH=\$SWOOLE_CLI_PATH
                export PKG_CONFIG_PATH=\$SWOOLE_CLI_PKG_CONFIG_PATH
EOF
            )
            ->withPkgName('valgrind')
            ->withBinPath($valgrind_prefix . '/bin/')
    );
}

function install_snappy(Preprocessor $p)
{

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
}

function install_fontconfig(Preprocessor $p)
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
            ->withCleanPreInstallDirectory('/usr/p11_kit/')
            ->withBuildScript(
                '

                # apk add python3 py3-pip  gettext  coreutils
                # pip3 install meson  -i https://pypi.tuna.tsinghua.edu.cn/simple


            # ./autogen.sh --prefix=/usr/p11_kit/ --disable-trust-module --disable-debug
            #  ./configure --help
            # --with-libtasn1 --with-libffi

            # meson setup -Dprefix=/usr/p11_kit/ -Dsystemd=disabled -Dbash_completion=disabled  --reconfigure  _build
            # run "ninja reconfigure" or "meson setup --reconfigure"
            # ninja reconfigure -C _build
            # meson setup --reconfigure _build

            meson setup  \
            -Dprefix=/usr/p11_kit/ \
            -Dsystemd=disabled  \
            -Dbash_completion=disabled \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true \
            -Ddebug=false \
            -Dstrict=true \
            -Dunity=off \
             _build


            # meson setup --wipe

           # DESTDIR=/usr/p11_kit/  meson install -C _build
            # meson install -C _build

            ninja  -C _build
            ninja  -C _build install

            '
            )
            ->withPkgName('p11_kit')
    );
}


function install_pgsql_test(Preprocessor $p)
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
            ->withConfigure(
                '
             # src/Makefile.shlib 有静态配置
             # src/interfaces/libpq/Makefile  有静态配置  参考：  install-lib install-lib-static  installdirs  installdirs-lib install-lib-pc

           # sed -i "s/ifndef haslibarule/ifndef custom_static/"  src/Makefile.shlib
           # sed -i "s/endif #haslibarule/endif #custom_static/"  src/Makefile.shlib
           sed -i "s/invokes exit\'; exit 1;/invokes exit\';/"  src/interfaces/libpq/Makefile
           # cp -rf  /work/Makefile.shlib src/Makefile.shlib
           # sed -i "120a \	echo \$<" src/interfaces/libpq/Makefile
           # sed -i "120a \	echo \$(PORTNAME)" src/interfaces/libpq/Makefile
           # 替换指定行内容
           sed -i "102c all: all-lib" src/interfaces/libpq/Makefile

           cat >> src/interfaces/libpq/Makefile <<"-EOF-"

libpq5555.a: $(OBJS) | $(SHLIB_PREREQS)
	echo $(SHLIB_PREREQS)
	echo $(SHLIB_LINK)
	echo $(exports_file)
	#rm -f $@
	rm -f libpq.a
	# ar  rcs $@  $^
	ar  rcs libpq.a  $^
	# ranlib $@
	ranlib libpq.a
	# touch $@
	# touch libpq.a
install-libpq5555.a: install-lib-static install-lib-pc
	$(MKDIR_P) "$(DESTDIR)$(libdir)" "$(DESTDIR)$(pkgconfigdir)"
	$(INSTALL_DATA) $(srcdir)/libpq-fe.h "$(DESTDIR)$(includedir)"
	$(INSTALL_DATA) $(srcdir)/libpq-events.h "$(DESTDIR)$(includedir)"
	$(INSTALL_DATA) $(srcdir)/libpq-int.h "$(DESTDIR)$(includedir_internal)"
	$(INSTALL_DATA) $(srcdir)/fe-auth-sasl.h "$(DESTDIR)$(includedir_internal)"
	$(INSTALL_DATA) $(srcdir)/pqexpbuffer.h "$(DESTDIR)$(includedir_internal)"
-EOF-

            export CPPFLAGS="-static -fPIE -fPIC -O2 -Wall "

            ./configure  --prefix=/usr/pgsql \
            --enable-coverage=no \
            --with-ssl=openssl  \
            --with-readline \
            --without-icu \
            --without-ldap \
            --without-libxml  \
            --without-libxslt \
            \
           --with-includes="/usr/openssl_3/include/:/usr/libxml2/include/:/usr/libxslt/include:/usr/zlib/include:/usr/include" \
           --with-libraries="/usr/openssl_3/lib64:/usr/libxslt/lib/:/usr/libxml2/lib/:/usr/zlib/lib:/usr/lib"
            # --with-includes="/usr/openssl_1/include/:/usr/libxml2/include/:/usr/libxslt/include:/usr/zlib/include:/usr/include" \
            # --with-libraries="/usr/openssl_1/lib:/usr/libxslt/lib/:/usr/libxml2/lib/:/usr/zlib/lib:/usr/lib"

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

            rm -rf /usr/pgsql/lib/*.so.*
            rm -rf /usr/pgsql/lib/*.so
            return 0
            make -C  src/interfaces/libpq -j $cpu_nums libpq5555.a
            make -C  src/interfaces/libpq install-libpq5555.a

            rm -rf /usr/pgsql/lib/*.so.*
            rm -rf /usr/pgsql/lib/*.so

            return 0

            nm -A /usr/pgsql/lib/libpq.a

            exit 0
            make -C src/interfaces/libpq  -j $cpu_nums  libpq.a
            exit 0
            make -C src/interfaces/libpq    install-libpq.a

            return 0

            rm -rf /usr/pgsql/lib/*.so.*
            rm -rf /usr/pgsql/lib/*.so

            return 0

            # need stage
            # src/include
            $ src/common
            # src/port
            # src/interfaces/libpq
            # src/bin/pg_config


            '
            )
            ->withMakeOptions('-C src/common all')
            ->withMakeInstallOptions('-C src/include install ')
            ->withPkgName('libpq')
            ->withPkgConfig('/usr/pgsql/lib/pkgconfig')
            ->withLdflags('-L/usr/pgsql/lib/')
            ->withBinPath('/usr/pgsql/bin/')
            ->withScriptAfterInstall(
                '
            '
            )
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


function install_jansson(Preprocessor $p)
{

}


function install_php_internal_extension_curl_patch(Preprocessor $p)
{

}


function install_libgomp(Preprocessor $p)
{

}

function install_libzip_ng(Preprocessor $p)
{
    $zlib_ng_prefix = '/usr/zlib_ng';
    $lib = new Library('zlib_ng');
    $lib->withHomePage('https://github.com/zlib-ng/zlib-ng.git')
        ->withLicense('https://github.com/zlib-ng/minizip-ng/blob/master/LICENSE', Library::LICENSE_SPEC)
        ->withUrl('https://github.com/zlib-ng/zlib-ng/archive/refs/tags/2.0.6.tar.gz')
        ->withManual('https://github.com/zlib-ng/zlib-ng.git')
        ->withPrefix($zlib_ng_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($zlib_ng_prefix)
        ->withConfigure(
            <<<EOF
./configure --help
EOF
        )
        ->withPkgName('zlib_ng');

    $p->addLibrary($lib);
}


function install_zookeeper_client($p)
{

}


function install_unixodbc(Preprocessor $p)
{

}


function install_xorg_macros(Preprocessor $p)
{
}

function install_xorgproto(Preprocessor $p)
{
    $xorgproto_prefix = XORGPROTO_PREFIX;
    $lib = new Library('xorgproto');
    $lib->withHomePage('xorgproto')
        ->withLicense('https://gitlab.freedesktop.org/xorg/proto/xorgproto', Library::LICENSE_SPEC)
        ->withManual('https://gitlab.freedesktop.org/xorg/proto/xorgproto/-/blob/master/INSTALL')
        ->withUrl('https://gitlab.freedesktop.org/xorg/proto/xorgproto/-/archive/master/xorgproto-master.tar.gz')
        ->withFile('xorgproto-2022.2.tar.gz')
        ->withDownloadScript(
            'xorgproto',
            <<<EOF
            git clone -b xorgproto-2022.2 --depth=1  https://gitlab.freedesktop.org/xorg/proto/xorgproto.git
EOF
        )
        ->withPrefix($xorgproto_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($xorgproto_prefix)
        ->withBuildScript(
            <<<EOF
            ls -lha .


            # https://mesonbuild.com/Builtin-options.html#build-type-options
            # meson configure build
            # meson wrap --help
            # --backend=ninja \

            meson setup  build \
            -Dprefix={$xorgproto_prefix} \
            -Dbackend=ninja \
            -Dbuildtype=release \
            -Ddefault_library=static \
            -Db_staticpic=true \
            -Db_pie=true \
            -Dprefer_static=true

            # meson configure build
            # meson -C build install

            ninja  -C build
            ninja  -C build install

EOF
        )
        ->withLdflags('');

    $p->addLibrary($lib);
}

function install_libX11(Preprocessor $p)
{
    $libX11_prefix = LIBX11_PREFIX;
    $lib = new Library('libX11');
    $lib->withHomePage('http://www.x.org/releases/current/doc/libX11/libX11/libX11.html')
        ->withLicense('https://github.com/mirror/libX11/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withManual('http://www.x.org/releases/current/doc/libX11/libX11/libX11.html')
        ->withUrl('https://github.com/mirror/libX11/archive/refs/tags/libX11-1.8.4.tar.gz')
        ->withFile('libX11-1.8.4.tar.gz')
        ->withPrefix($libX11_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libX11_prefix)
        ->withConfigure(
            <<<EOF
        ./autogen.sh
        ./configure --help
        ./configure \
        --prefix={$libX11_prefix} \
        --enable-shared=no \
        --enable-static=yes
EOF
        )
        ->withLdflags('');

    $p->addLibrary($lib);
}


function install_opencl(Preprocessor $p)
{
}

function install_boost(Preprocessor $p)
{
}