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
    $libyuv_prefix = LIBYUV_PREFIX;
    $libjpeg_prefix = JPEG_PREFIX;
    $p->addLibrary(
        (new Library('libyuv'))
            ->withUrl('https://chromium.googlesource.com/libyuv/libyuv')
            ->withHomePage('https://chromium.googlesource.com/libyuv/libyuv')
            ->withLicense(
                'https://chromium.googlesource.com/libyuv/libyuv/+/refs/heads/main/LICENSE',
                Library::LICENSE_SPEC
            )
            ->withManual('https://chromium.googlesource.com/libyuv/libyuv/+/HEAD/docs/getting_started.md')
            ->disableDownloadWithMirrorURL()
            ->withDownloadScript(
                'libyuv',
                <<<EOF
            git clone -b main --depth=1 https://chromium.googlesource.com/libyuv/libyuv
EOF
            )
            //->withUntarArchiveCommand('cp')
            ->withPrefix($libyuv_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libyuv_prefix)
            ->withBuildScript(
                <<<EOF
                mkdir -p  out
                cd out
                cmake .. \
                -DCMAKE_INSTALL_PREFIX="{$libyuv_prefix}" \
                -DJPEG_ROOT={$libjpeg_prefix} \
                -DBUILD_STATIC_LIBS=ON \
                -DBUILD_SHARED_LIBS=OFF  \
                -DCMAKE_BUILD_TYPE="Release" \
                -DTEST=OFF
                cmake --build . --config Release
                cmake --build . --target install --config Release
                return 0
                rm -rf {$libyuv_prefix}/lib/*.so.*
                rm -rf {$libyuv_prefix}/lib/*.so
                rm -rf {$libyuv_prefix}/lib/*.dylib
:<<'_____EOF_____'
            make V=1 -f linux.mk
            make V=1 -f linux.mk clean
            make V=1 -f linux.mk CXX=clang++ CC=clang
            exit 0
            gn gen out/Release "--args=is_debug=false"
            gn gen out/Debug "--args=is_debug=true"
            ninja -v -C out/Release
            ninja -v -C out/Debug

            exit  0


            #  cmake默认查找到的是动态库 ; cmake 优先使用静态库
            #  参考 https://blog.csdn.net/10km/article/details/82931978

            # sed -i '/find_package ( JPEG )/i set( JPEG_NAMES libjpeg.a )'  CMakeLists.txt

            # -DJPEG_LIBRARY_RELEASE={$libjpeg_prefix}/lib/libjpeg.a
            # CMAKE_INCLUDE_PATH 和 CMAKE_LIBRARY_PATH

            # -DJPEG_LIBRARY:PATH={$libjpeg_prefix}/lib/libjpeg.a -DJPEG_INCLUDE_DIR:PATH={$libjpeg_prefix}/include/ \

            mkdir -p build
            cd build
            cmake \
            -Wno-dev \
            -DCMAKE_INSTALL_PREFIX="{$libyuv_prefix}" \
            -DCMAKE_BUILD_TYPE="Release"  \
            -DJPEG_LIBRARY:PATH={$libjpeg_prefix}/lib/libjpeg.a -DJPEG_INCLUDE_DIR:PATH={$libjpeg_prefix}/include/ \
            -DBUILD_SHARED_LIBS=OFF  ..

            cmake --build . --config Release
            cmake --build . --target install --config Release

_____EOF_____
EOF
            )
            ->withPkgName('')
            ->withBinPath($libyuv_prefix . '/bin/')
    );
}


function install_libraw(Preprocessor $p)
{
    $link_cpp = $p->getOsType() == 'macos' ? '-lc++' : '-lstdc++';
    $libraw_prefix = LIBRAW_PREFIX;
    $lib = new Library('libraw');
    $lib->withHomePage('https://www.libraw.org/about')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('https://www.libraw.org/data/LibRaw-0.21.1.tar.gz')
        ->withPrefix($libraw_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libraw_prefix)
        ->withConfigure(
            <<<EOF
            ./configure --help
            echo {$link_cpp}

            set -uex
            package_names="zlib libjpeg libturbojpeg lcms2"

            CPPFLAGS="\$(pkg-config  --cflags-only-I --static \$package_names )" \
            LDFLAGS="\$(pkg-config   --libs-only-L   --static \$package_names )" \
            LIBS="\$(pkg-config      --libs-only-l   --static \$package_names ) {$link_cpp}" \
            ./configure \
            --prefix={$libraw_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-jpeg \
            --enable-zlib \
            --enable-lcms \
            --disable-jasper  \
            --disable-openmp

EOF
        )
        ->withPkgName('libraw')
        ->withPkgName('libraw_r')
        ->withScriptAfterInstall(
            <<<EOF
        ls -lh {$libraw_prefix}/lib/pkgconfig/libraw.pc
        ls -lh {$libraw_prefix}/lib/pkgconfig/libraw_r.pc
        # sed  "s/-lstdc++/-lc++/g" '{$libraw_prefix}/lib/pkgconfig/libraw.pc'
        # sed  "s/-lstdc++/-lc++/g" '{$libraw_prefix}/lib/pkgconfig/libraw_r.pc'
EOF
        )
        ->withBinPath($libraw_prefix . '/bin/');

    $p->addLibrary($lib);
}

function install_dav1d(Preprocessor $p)
{
    $dav1d_prefix = DAV1D_PREFIX;
    $p->addLibrary(
        (new Library('dav1d'))
            ->withHomePage('https://code.videolan.org/videolan/dav1d/')
            ->withLicense('https://code.videolan.org/videolan/dav1d/-/blob/master/COPYING', Library::LICENSE_BSD)
            ->withUrl('https://code.videolan.org/videolan/dav1d/-/archive/1.1.0/dav1d-1.1.0.tar.gz')
            ->withFile('dav1d-1.1.0.tar.gz')
            ->withManual('https://code.videolan.org/videolan/dav1d')
            ->disableDownloadWithMirrorURL()
            ->withPrefix($dav1d_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($dav1d_prefix)
            ->withBuildScript(
                <<<EOF

                mkdir -p build
                cd build
                meson setup \
                --backend=ninja \
                --prefix={$dav1d_prefix} \
                --default-library=static \
                ..
                ninja
                ninja install


EOF
            )
            ->withPkgName('dav1d')
            ->withBinPath($dav1d_prefix . '/bin/')
    );
}

function install_libgav1(Preprocessor $p)
{
    $libgav1_prefix = LIBGAV1_PREFIX;
    $p->addLibrary(
        (new Library('libgav1'))
            ->withHomePage('https://chromium.googlesource.com/codecs/libgav1')
            ->withLicense(
                'https://chromium.googlesource.com/codecs/libgav1/+/refs/heads/main/LICENSE',
                Library::LICENSE_APACHE2
            )
            ->withFile('libgav1.tar.gz')
            ->withManual('https://chromium.googlesource.com/codecs/libgav1/+/refs/heads/main')
            ->withDownloadScript(
                'libgav1',
                <<<EOF
                git clone --depth 1  https://chromium.googlesource.com/codecs/libgav1
                mkdir -p libgav1/third_party/abseil-cpp
                git clone -b 20220623.0 --depth 1 https://github.com/abseil/abseil-cpp.git libgav1/third_party/abseil-cpp
EOF
            )
            ->disableDownloadWithMirrorURL()
            ->withPrefix($libgav1_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libgav1_prefix)
            ->withConfigure(
                <<<EOF
                mkdir -p build
                cd build
                # 查看更多选项
                # cmake .. -LH
                cmake -G "Unix Makefiles" .. \
                -DCMAKE_INSTALL_PREFIX={$libgav1_prefix} \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_SHARED_LIBS=OFF  \
                -DBUILD_STATIC_LIBS=ON \
                -DLIBGAV1_ENABLE_TESTS=OFF

EOF
            )
            ->withPkgName('libgav1')
            ->withBinPath($libgav1_prefix . '/bin/')
    );
}

function install_libavif(Preprocessor $p): void
{
    $libavif_prefix = LIBAVIF_PREFIX;
    $libyuv_prefix = LIBYUV_PREFIX;
    $dav1d_prefix = DAV1D_PREFIX;
    $libgav1_prefix = LIBGAV1_PREFIX;
    $aom_prefix = AOM_PREFIX;
    $p->addLibrary(
        (new Library('libavif'))
            ->withUrl('https://github.com/AOMediaCodec/libavif/archive/refs/tags/v0.11.1.tar.gz')
            ->withFile('libavif-v0.11.1.tar.gz')
            ->withHomePage('https://aomediacodec.github.io/av1-avif/')
            ->withLicense('https://github.com/AOMediaCodec/libavif/blob/main/LICENSE', Library::LICENSE_SPEC)
            ->withManual('https://github.com/AOMediaCodec/libavif')
            ->disableDownloadWithMirrorURL()
            ->withPrefix($libavif_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libavif_prefix)
            ->withConfigure(
                <<<EOF

            cmake .  \
            -DCMAKE_INSTALL_PREFIX={$libavif_prefix} \
            -DAVIF_BUILD_EXAMPLES=OFF \
            -DLIBYUV_ROOT={$libyuv_prefix} \
            -DDAV1D_ROOT={$dav1d_prefix} \
            -DLIBGAV1_ROOT={$libgav1_prefix} \
            -DBUILD_SHARED_LIBS=OFF \
            -DAVIF_CODEC_AOM=ON \
            -DAVIF_CODEC_DAV1D=ON \
            -DAVIF_CODEC_LIBGAV1=ON \
            -DAVIF_CODEC_RAV1E=OFF


EOF
            )
            ->withPkgName('libavif')
            ->withLdflags('')
    );
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
            ->disableDownloadWithMirrorURL()
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
    $svt_av1_prefix = SVT_AV1_PREFIX;
    $lib = new Library('svt_av1');
    $lib->withHomePage('https://gitlab.com/AOMediaCodec/SVT-AV1/')
        ->withLicense('https://gitlab.com/AOMediaCodec/SVT-AV1/-/blob/master/LICENSE.md', Library::LICENSE_BSD)
        ->withManual('https://gitlab.com/AOMediaCodec/SVT-AV1/-/blob/master/Docs/Build-Guide.md')
        ->withUrl('https://gitlab.com/AOMediaCodec/SVT-AV1/-/archive/v1.4.1/SVT-AV1-v1.4.1.tar.gz')
        ->withFile('SVT-AV1-v1.4.1.tar.gz')
        ->withPrefix($svt_av1_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($svt_av1_prefix)
        ->withConfigure(
            <<<EOF
        cd SVT-AV1
        cd Build
        cmake .. -G"Unix Makefiles" \
        -DCMAKE_INSTALL_PREFIX={$svt_av1_prefix} \
        -DCMAKE_BUILD_TYPE=Release  \
        -DBUILD_SHARED_LIBS=OFF  \
        -DBUILD_STATIC_LIBS=ON

EOF
        )
        ->withPkgName('SvtAv1Dec')
        ->withPkgName('SvtAv1Enc')
        ->withBinPath($svt_av1_prefix . '/bin/');

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
    $libfribidi_prefix = LIBFRIBIDI_PREFIX;
    $p->addLibrary(
        (new Library('libfribidi'))
            ->withLicense('https://github.com/fribidi/fribidi/blob/master/COPYING', Library::LICENSE_LGPL)
            ->withHomePage('https://github.com/fribidi/fribidi.git')
            ->withUrl('https://github.com/fribidi/fribidi/archive/refs/tags/v1.0.12.tar.gz')
            ->withFile('fribidi-v1.0.12.tar.gz')
            ->withLabel('library')
            ->withPrefix($libfribidi_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libfribidi_prefix)
            ->withConfigure(
                "

                # 可以使用 meson
                # meson setup  build

                sh autogen.sh
                ./configure --help

                ./configure \
                --prefix={$libfribidi_prefix} \
                --enable-static=yes \
                --enable-shared=no
            "
            )
            ->withPkgName('harfbuzz-icu  harfbuzz-subset harfbuzz')
    );
}

function install_harfbuzz(Preprocessor $p)
{
    $harfbuzz_prefix = HARFBUZZ_PREFIX;
    $p->addLibrary(
        (new Library('harfbuzz'))
            ->withLicense('https://github.com/harfbuzz/harfbuzz/blob/main/COPYING', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/harfbuzz/harfbuzz.git')
            ->withUrl('https://github.com/harfbuzz/harfbuzz/archive/refs/tags/7.1.0.tar.gz')
            ->withFile('harfbuzz-7.1.0.tar.gz')
            ->withLabel('library')
            ->withPrefix($harfbuzz_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($harfbuzz_prefix)
            ->withBuildScript(
                <<<EOF
                meson help
                meson setup --help

                meson setup  build \
                --backend=ninja \
                --prefix={$harfbuzz_prefix} \
                --default-library=static \
                -Dglib=disabled \
                -Dicu=enabled \
                -Dfreetype=disabled \
                -Dtests=disabled \
                -Ddocs=disabled  \
                -Dbenchmark=disabled

                meson compile -C build
                meson install -C build
                # ninja -C build
                # ninja -C build install

EOF
            )
            ->withPkgName('harfbuzz-icu  harfbuzz-subset harfbuzz')
    );
}


//-lgd -lpng -lz -ljpeg -lfreetype -lm

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
    $libOpenEXR_prefix = '/usr/libOpenEXR';
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
        # cmake .  -DCMAKE_INSTALL_PREFIX={$libOpenEXR_prefix}

        cmake.   --install-prefix={$libOpenEXR_prefix}
        cmake --build .  --target install --config Release
EOF
        )
        ->withPkgName('Imath OpenEXR')
        ->withBinPath('$libOpenEXR_prefix' . '/bin');

    $p->addLibrary($lib);
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
        git init -b main .
        git add ./.gitmodules

        git -C . submodule update --init --recursive --depth 1 --recommend-shallow

        sh deps.sh

        git submodule update --init --recursive
        exit 0
        mkdir -p build
        cd build
        cmake -DJPEGXL_STATIC=true \
        -DCMAKE_BUILD_TYPE=Release \
        -DBUILD_SHARED_LIBS=OFF \
        -DBUILD_TESTING=OFF \
        -DCMAKE_INSTALL_PREFIX={$libjxl_prefix} \
         ..

        cmake --build . -- -j$(nproc)
        cmake --install .
EOF
        )
        ->withPkgName('libjxl');

    $p->addLibrary($lib);
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


function install_libdeflate(Preprocessor $p)
{
    $libdeflate_prefix = '/usr/libdeflate';
    $p->addLibrary(
        (new Library('libdeflate'))
            ->withLicense('https://github.com/ebiggers/libdeflate/blob/master/COPYING', Library::LICENSE_MIT)
            ->withHomePage('https://github.com/ebiggers/libdeflate.git')
            ->withUrl('https://github.com/ebiggers/libdeflate/archive/refs/tags/v1.17.tar.gz')
            ->withFile('libdeflate-v1.17.tar.gz')
            ->withLabel('library')
            ->withPrefix($libdeflate_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libdeflate_prefix)
            ->withConfigure(
                "
                ls -lh
                exit 0
                cmake -B build && cmake --build build

            "
            )
            ->withPkgName('libdeflate')
            ->depends('libzip', 'zlib')
    );
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

function install_nettle($p)
{
    $nettle_prefix = NETTLE_PREFIX;
    $p->addLibrary(
        (new Library('nettle'))
            ->withHomePage('https://www.lysator.liu.se/~nisse/nettle/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://ftp.gnu.org/gnu/nettle/nettle-3.8.tar.gz')
            ->withFile('nettle-3.8.tar.gz')
            ->withPrefix($nettle_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($nettle_prefix)
            ->withConfigure(
                <<<EOF
             ./configure --help
            ./configure \
            --prefix={$nettle_prefix} \
            --enable-static \
            --disable-shared \
            --enable-mini-gmp
EOF
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
    $libexpat_prefix = LIBEXPAT_PREFIX;
    $p->addLibrary(
        (new Library('libexpat'))
            ->withHomePage('https://github.com/libexpat/libexpat')
            ->withLicense('https://github.com/libexpat/libexpat/blob/master/COPYING', Library::LICENSE_MIT)
            ->withManual('https://libexpat.github.io/doc/')
            ->withUrl('https://github.com/libexpat/libexpat/releases/download/R_2_5_0/expat-2.5.0.tar.gz')
            ->withPrefix($libexpat_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($libexpat_prefix)
            ->withConfigure(
                <<<EOF
             ./configure --help

            ./configure \
            --prefix={$libexpat_prefix} \
            --enable-static=yes \
            --enable-shared=no
EOF
            )->withPkgName('expat')
            ->withBinPath($libexpat_prefix . '/bin')
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
            ->disableDownloadWithMirrorURL()
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
    $nghttp3_prefix = NGHTTP3_PREFIX;
    $p->addLibrary(
        (new Library('nghttp3'))
            ->withHomePage('https://github.com/ngtcp2/nghttp3')
            ->withLicense('https://github.com/ngtcp2/nghttp3/blob/main/COPYING', Library::LICENSE_MIT)
            ->withManual('https://nghttp2.org/nghttp3/')
            ->withUrl('https://github.com/ngtcp2/nghttp3/archive/refs/tags/v0.9.0.tar.gz')
            //->withUrl('https://github.com/ngtcp2/nghttp3/archive/refs/heads/main.zip')
            ->withFile('nghttp3-v0.9.0.tar.gz')
            ->disableDownloadWithMirrorURL()
            ->withPrefix($nghttp3_prefix)
            ->withConfigure(
                <<<EOF
            autoreconf -fi
            ./configure --help
            ./configure --prefix={$nghttp3_prefix} \
            --enable-lib-only \
            --enable-shared=no \
            --enable-static=yes
EOF
            )
            ->withPkgName('libnghttp3')
    );
}

function install_ngtcp2(Preprocessor $p)
{
    $ngtcp2_prefix = NGTCP2_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;
    $p->addLibrary(
        (new Library('ngtcp2'))
            ->withHomePage('https://github.com/ngtcp2/ngtcp2')
            ->withLicense('https://github.com/ngtcp2/ngtcp2/blob/main/COPYING', Library::LICENSE_MIT)
            ->withManual('https://curl.se/docs/http3.html')
            ->withUrl('https://github.com/ngtcp2/ngtcp2/archive/refs/tags/v0.13.1.tar.gz')
            ->withFile('ngtcp2-v0.13.1.tar.gz')
            ->disableDownloadWithMirrorURL()
            ->withPrefix($ngtcp2_prefix)
            ->withConfigure(
                <<<EOF
                autoreconf -fi
                ./configure --help

                PACKAGES="openssl libnghttp3 "
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES )"  \
                LDFLAGS="$(pkg-config --libs-only-L      --static \$PACKAGES )"  \
                LIBS="$(pkg-config --libs-only-l         --static \$PACKAGES )"  \
                ./configure \
                --prefix=$ngtcp2_prefix \
                --enable-shared=no \
                --enable-static=yes \
                --enable-lib-only \
                --without-libev \
                --with-openssl  \
                --with-libnghttp3=yes \
                --without-gnutls \
                --without-boringssl \
                --without-picotls \
                --without-wolfssl \
                --without-cunit  \
                --without-jemalloc
EOF
            )
            ->withPkgName('libngtcp2')
            ->withPkgName('libngtcp2_crypto_openssl')
            ->depends('openssl', 'nghttp3')
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
    $nghttp2_prefix = NGHTTP2_PREFIX;
    $p->addLibrary(
        (new Library('nghttp2'))
            ->withHomePage('https://github.com/nghttp2/nghttp2.git')
            ->withUrl('https://github.com/nghttp2/nghttp2/releases/download/v1.51.0/nghttp2-1.51.0.tar.gz')
            ->withPrefix($nghttp2_prefix)
            ->withConfigure(
                <<<EOF
            ./configure --help
            packages="zlib libxml-2.0 libcares openssl "  # jansson  libev libbpf libelf libngtcp2 libnghttp3
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$packages )"  \
            LDFLAGS="$(pkg-config --libs-only-L      --static \$packages )"  \
            LIBS="$(pkg-config --libs-only-l         --static \$packages )"  \
            ./configure --prefix={$nghttp2_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --enable-lib-only \
            --with-libxml2  \
            --with-zlib \
            --with-libcares \
            --with-openssl \
            --disable-http3 \
            --disable-python-bindings  \
            --without-jansson  \
            --without-libevent-openssl \
            --without-libev \
            --without-cunit \
            --without-jemalloc \
            --without-mruby \
            --without-neverbleed \
            --without-cython \
            --without-libngtcp2 \
            --without-libnghttp3  \
            --without-libbpf   \
            --with-boost=no
EOF
            )
            ->withLicense('https://github.com/nghttp2/nghttp2/blob/master/COPYING', Library::LICENSE_MIT)
            ->withPkgName('libnghttp2')
            ->depends('openssl', 'zlib', 'libxml2', 'cares')
    );
}


function install_libunistring($p)
{
    $iconv_prefix = ICONV_PREFIX;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $p->addLibrary(
        (new Library('libunistring'))
            ->withHomePage('https://www.gnu.org/software/libunistring/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
            ->withUrl('https://ftp.gnu.org/gnu/libunistring/libunistring-1.1.tar.gz')
            ->withPrefix($libunistring_prefix)
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
            ./configure --help
            ./configure \
            --prefix={$libunistring_prefix} \
            --with-libiconv-prefix={$iconv_prefix} \
            --enable-static \
            --disable-shared
EOF
            )
            ->withPkgName('libunistring')
    );
}

function install_libintl(Preprocessor $p)
{
    $gettext_prefix = GETTEXT_PREFIX ;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $ncurses_prefix = NCURSES_PREFIX;
    $libintl_prefix =  LIBINTL_PREFIX;
    $p->addLibrary(
        (new Library('libintl'))
            ->withUrl('https://ftp.gnu.org/gnu/gettext/gettext-0.21.1.tar.gz')
            ->withHomePage('https://www.gnu.org/software/gettext/')
            ->withLicense('https://www.gnu.org/licenses/licenses.html', Library::LICENSE_GPL)
            ->withCleanBuildDirectory()
            ->withPrefix($libintl_prefix)
            ->withConfigure(
                <<<EOF
             cd gettext-runtime/

            ./configure \
            --prefix={$libintl_prefix} \
             --enable-shared=yes \
             --enable-static=no \
             --enable-relocatable \
             --with-libiconv-prefix=${iconv_prefix} \
             --with-libncurses-prefix=${ncurses_prefix} \
             --with-libxml2-prefix=${libxml2_prefix} \
             --with-libunistring-prefix=${libunistring_prefix} \
             --without-libintl-prefix \
             --without-libtermcap-prefix \
             --without-emacs \
             --without-lispdir \
             --without-cvs \
              --without-included-regex \
              --without-libtextstyle-prefix \
              --disable-libasprintf \
              --disable-openmp \
              --disable-acl \
              --disable-java \
              --disable-csharp \
               --without-git \
               --disable-nls \
               --disable-namespacing

EOF
            )
            ->withPkgName('gettext')
            ->withMakeInstallOptions('DESTDIR=' . $libintl_prefix)
    );
}
function install_gettext(Preprocessor $p)
{
    $gettext_prefix = GETTEXT_PREFIX ;
    $libunistring_prefix = LIBUNISTRING_PREFIX;
    $iconv_prefix = ICONV_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $ncurses_prefix = NCURSES_PREFIX;
    $p->addLibrary(
        (new Library('gettext'))
            ->withUrl('https://ftp.gnu.org/gnu/gettext/gettext-0.21.1.tar.gz')
            ->withHomePage('https://www.gnu.org/software/gettext/')
            ->withLicense('https://www.gnu.org/licenses/licenses.html', Library::LICENSE_GPL)
            ->withManual('https://www.jiangguo.net/c/1q/nZW.html')
            ->withCleanBuildDirectory()
            ->withPrefix($gettext_prefix)
            ->withConfigure(
                <<<EOF
            cd gettext-tools
            ./configure --help
            exit 3
            ./configure \
            --prefix={$gettext_prefix} \
             --enable-shared=yes \
             --enable-static=no \
             --enable-relocatable \
             --with-libiconv-prefix=${iconv_prefix} \
             --with-libncurses-prefix=${ncurses_prefix} \
             --with-libxml2-prefix=${libxml2_prefix} \
             --with-libunistring-prefix=${libunistring_prefix} \
             --without-libintl-prefix \
             --without-libtermcap-prefix \
             --without-emacs \
             --without-lispdir \
             --without-cvs \
              --without-included-regex \
              --without-libtextstyle-prefix \
              --disable-libasprintf \
              --disable-openmp \
              --disable-acl \
              --disable-java \
              --disable-csharp \
               --without-git \
               --disable-nls \
               --disable-namespacing

              # make -C lib
              # make -C src msgfmt
EOF
            )
            ->withPkgName('gettext')
            ->withMakeOptions("lib")
            ->withMakeInstallOptions('lib')
    );
}

function install_coreutils($p): void
{
    /*
        glibc是一个核心C运行时库.它提供了像printf(3)和的东西fopen(3).

        glib 是一个用C编写的基于对象的事件循环和实用程序库.

        gnulib 是一个库,提供从POSIX API到本机API的适配器.

        coreutils    包括常用的命令，如 cat、ls、rm、chmod、mkdir、wc、whoami 和许多其他命令

     */
    $iconv_prefix = ICONV_PREFIX;
    $gmp_prefix = GMP_PREFIX;
    $libintl_prefix = LIBINTL_PREFIX ;
    $p->addLibrary(
        (new Library('coreutils'))
            ->withHomePage('https://www.gnu.org/software/coreutils/')
            ->withLicense('https://www.gnu.org/licenses/gpl-2.0.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/coreutils/')
            ->withUrl('https://mirrors.aliyun.com/gnu/coreutils/coreutils-9.1.tar.gz')
            ->withFile('coreutils-9.1.tar.gz')
            ->withCleanBuildDirectory()
            ->withBuildScript(
                <<<EOF

                ./configure --help

                ./bootstrap
                ./configure \
                --prefix=/usr/coreutils \
                --with-openssl=yes \
                --with-libiconv-prefix={$iconv_prefix} \
                --with-libgmp-prefix={$gmp_prefix} \
                --with-libintl-prefix={ $libintl_prefix}

EOF
            )
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

       Gnulib，也称为GNU Portability Library，是 GNU 代码的集合，用于帮助编写可移植代码。

     */
    $p->addLibrary(
        (new Library('gnulib'))
            ->withHomePage('https://www.gnu.org/software/gnulib/')
            ->withLicense('https://www.gnu.org/licenses/gpl-2.0.html', Library::LICENSE_LGPL)
            ->withManual('https://www.gnu.org/software/gnulib/manual/')
            ->withManual('https://www.gnu.org/software/gnulib/manual/html_node/Building-gnulib.html')
            ->withUrl('https://github.com/coreutils/gnulib/archive/refs/heads/master.zip')
            ->withDownloadScript(
                'gnulib',
                <<<EOF
              git clone --depth 1 --single-branch  https://git.savannah.gnu.org/git/gnulib.git
EOF
            )
            ->withFile('gnulib-latest.tar.gz')
            ->withCleanBuildDirectory()
            ->withBuildScript(
                <<<EOF

                ./gnulib-tool --help
                exit 3
                gnulib-tool --create-megatestdir --with-tests --dir=...
                ./configure
                make dist
                ./do-autobuild
                             return 0 ;
EOF
            )
            ->withPkgConfig('')
            ->withPkgName('')
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
                # apk add bazel
                # https://pkgs.alpinelinux.org/packages?name=bazel*&branch=edge&repo=testing&arch=&maintainer=
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
            ->withManual('https://libbpf.readthedocs.io/en/latest/libbpf_build.html')
            ->withPrefix('/usr/libbpf')
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
                cd src
                BUILD_STATIC_ONLY=y  make
                exit 0
                mkdir build /usr/libbpf
                PKG_CONFIG_PATH=/usr/libbpf/lib64/pkgconfig
                BUILD_STATIC_ONLY=y \
                OBJDIR=build \
                DESTDIR=/usr/libbpf \
                make install
                eixt 0

EOF
            )
            ->withPkgName('libbpf')
    );
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
    $snappy_prefix = SNAPPY_PREFIX;
    $p->addLibrary(
        (new Library('snappy'))
            ->withHomePage('https://github.com/google/snappy')
            ->withLicense('https://github.com/google/snappy/blob/main/COPYING', Library::LICENSE_BSD)
            ->withUrl('https://github.com/google/snappy/archive/refs/tags/1.1.10.tar.gz')
            ->withFile('snappy-1.1.10.tar.gz')
            ->withManual('https://github.com/google/snappy/blob/main/README.md')
            ->withDownloadScript(
                'snappy',
                <<<EOF
                git clone -b 1.1.10 --depth 1  --recursive  https://github.com/google/snappy


EOF
            )
            ->withPrefix($snappy_prefix)
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF

                mkdir -p build
                cd build
                cmake .. \
                -Werror -Wsign-compare \
                -DCMAKE_INSTALL_PREFIX={$snappy_prefix} \
                -DCMAKE_INSTALL_LIBDIR={$snappy_prefix}/lib \
                -DCMAKE_INSTALL_INCLUDEDIR={$snappy_prefix}/include \
                -DCMAKE_BUILD_TYPE=Release  \
                -DBUILD_SHARED_LIBS=OFF  \
                -DBUILD_STATIC_LIBS=ON

EOF
            )
            ->withPkgName('snappy')
            ->withBinPath($snappy_prefix . '/bin/')
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

function install_pcre2(Preprocessor $p)
{
    $pcre2_prefix = PCRE2_PREFIX;
    $p->addLibrary(
        (new Library('pcre2'))
            ->withHomePage('https://github.com/PCRE2Project/pcre2.git')
            ->withUrl('https://github.com/PCRE2Project/pcre2/releases/download/pcre2-10.42/pcre2-10.42.tar.gz')
            ->withDocumentation('https://pcre2project.github.io/pcre2/doc/html/index.html')
            ->withManual('https://github.com/PCRE2Project/pcre2.git')
            ->withLicense(
                'https://github.com/PCRE2Project/pcre2/blob/master/COPYING',
                Library::LICENSE_SPEC
            )
            ->withFile('pcre2-10.42.tar.gz')
            ->withPrefix($pcre2_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($pcre2_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help

                ./configure \
                --prefix=$pcre2_prefix \
                --enable-shared=no \
                --enable-static=yes \
                --enable-pcre2-16 \
                --enable-pcre2-32 \
                --enable-jit \
                --enable-unicode


 EOF
            )
        //->withPkgName("libpcrelibpcre2-32libpcre2-8 libpcre2-posix")
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
            ->withCleanPreInstallDirectory($jansson_prefix)
            ->withConfigure(
                <<<EOF
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


function install_php_internal_extension_curl_patch(Preprocessor $p)
{
    $workDir = $p->getWorkDir();
    $command = '';

    if (is_file("{$workDir}/ext/curl/config.m4.backup")) {
        $originFileHash = md5(file_get_contents("{$workDir}/ext/curl/config.m4"));
        $backupFileHash = md5(file_get_contents("{$workDir}/ext/curl/config.m4.backup"));
        if ($originFileHash == $backupFileHash) {
            $command = <<<EOF
           test -f {$workDir}/ext/curl/config.m4.backup && rm -f {$workDir}/ext/curl/config.m4.backup
           test -f {$workDir}/ext/curl/config.m4.backup ||  sed -i.backup '75,82d' {$workDir}/ext/curl/config.m4
EOF;
        }
    } else {
        $command = <<<EOF
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
            ->withConfigure('return 0 ')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
    );
}


function install_libgomp(Preprocessor $p)
{
    $libgomp_prefix = '/usr/libgomp';
    $lib = new Library('libgomp');
    $lib->withHomePage('https://gcc.gnu.org/projects/gomp/')
        ->withLicense('http://www.gnu.org/licenses/lgpl-2.1.html', Library::LICENSE_LGPL)
        ->withUrl('')
        ->withManual('https://gcc.gnu.org/onlinedocs/libgomp/')
        ->withPrefix($libgomp_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($libgomp_prefix)
        ->withConfigure(
            <<<EOF
./configure --help
EOF
        )
        ->withPkgName('libgomp');

    $p->addLibrary($lib);
}

function install_libzip_ng(Preprocessor $p)
{
    $zlib_ng_prefix = '/usr/zlib_ng';
    $lib = new Library('libgomp');
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
    $workDir = $p->getWorkDir();
    $openssl_prefix = OPENSSL_PREFIX;
    $zookeeper_client_prefix = '/usr/zookeeper_client';
    $p->addLibrary(
        (new Library('zookeeper_client'))
            ->withHomePage('https://zookeeper.apache.org/')
            ->withLicense('https://www.apache.org/licenses/', Library::LICENSE_APACHE2)
            //->withUrl('https://www.apache.org/dyn/closer.lua/zookeeper/zookeeper-3.8.1/apache-zookeeper-3.8.1.tar.gz')
            ->withUrl('https://dlcdn.apache.org/zookeeper/zookeeper-3.8.1/apache-zookeeper-3.8.1.tar.gz')
            ->withManual('https://zookeeper.apache.org/doc/r3.8.1/zookeeperStarted.html')
            ->withLabel('library')
            ->withCleanBuildDirectory()
            ->withBuildScript(
                <<<EOF
             ant compile_jute
            cd zookeeper-client/zookeeper-client-c
            cmake .
            -DCMAKE_BUILD_TYPE=Release \
            -DCMAKE_INSTALL_PREFIX="{$zookeeper_client_prefix}" \
            -DWANT_CPPUNIT=OFF \
            -DWITH_OPENSSL=ON  \
            -DBUILD_SHARED_LIBS=OFF

            cmake --build .
EOF
            )
            ->withConfigure(
                <<<EOF

            ant compile_jute
            cd zookeeper-client/zookeeper-client-c
            autoreconf -if
            ./configure --help

            ./configure \
            --prefix={$zookeeper_client_prefix} \
            --enable-shared=no \
            --enable-static=yes  \
            --with-openssl={$openssl_prefix} \
            --without-cppunit

EOF
            )
            //->withSkipDownload()
            ->disablePkgName()
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->withSkipBuildLicense()
        // ->withSkipBuildInstall()
    );
}


function install_unixodbc(Preprocessor $p)
{
    $unixODBC_prefix = UNIX_ODBC_PREFIX;
    $p->addLibrary(
        (new Library('unixodbc'))
            ->withHomePage('https://www.unixodbc.org/')
            ->withUrl('https://www.unixodbc.org/unixODBC-2.3.11.tar.gz')
            ->withLicense('https://github.com/lurcher/unixODBC/blob/master/LICENSE', Library::LICENSE_LGPL)
            ->withManual('https://www.unixodbc.org/doc/')
            ->withManual('https://github.com/lurcher/unixODBC.git')
            ->withLabel('build_env_bin')
            ->withCleanBuildDirectory()
            ->withConfigure(
                "
                autoreconf -fi
             ./configure --help
             ./configure \
             --prefix={$unixODBC_prefix} \
             --enable-shared=no \
             --enable-static=yes \
             --enable-iconv \
             --enable-readline \
             --enable-threads
            "
            )
            ->withBinPath($unixODBC_prefix . '/bin/')
            ->disablePkgName()
    );
}


function install_xorg_macros(Preprocessor $p)
{
    $xorg_macros_prefix = XORG_MACROS_PREFIX;
    $lib = new Library('xorg_macros');
    $lib->withHomePage('https://github.com/freedesktop/xorg-macros.git')
        ->withLicense('https://github.com/freedesktop/xorg-macros/blob/master/COPYING', Library::LICENSE_SPEC)
        ->withManual('https://gitlab.freedesktop.org/xorg/util/macros/-/blob/util-macros-1.20.0/INSTALL')
        ->withUrl(
            'https://gitlab.freedesktop.org/xorg/util/macros/-/archive/util-macros-1.20.0/macros-util-macros-1.20.0.tar.gz'
        )
        ->withFile('util-macros-1.20.0.tar.gz')
        ->withDownloadScript(
            'macros',
            <<<EOF
            git clone -b util-macros-1.20.0 --depth=1  https://gitlab.freedesktop.org/xorg/util/macros.git
EOF
        )
        ->withPrefix($xorg_macros_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($xorg_macros_prefix)
        ->withConfigure(
            <<<EOF
            ls -lha .
             ./autogen.sh
             ./configure --help
             ./configure \
             --prefix={$xorg_macros_prefix}

EOF
        )
        ->withMakeOptions('all')
    ;

    $p->addLibrary($lib);
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
    $opencl_prefix = LIBXPM_PREFIX;
    $opencl_prefix = '/usr/opencl';
    $lib = new Library('opencl');
    $lib->withHomePage('https://www.khronos.org/opencl/')
        ->withLicense('https://github.com/KhronosGroup/OpenCL-SDK/blob/main/LICENSE', Library::LICENSE_APACHE2)
        ->withUrl('git clone --recursive https://github.com/KhronosGroup/OpenCL-SDK.git')
        ->withPrefix($opencl_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($opencl_prefix)
        ->withBuildScript(
            <<<EOF
      cmake -A x64 \
      -D BUILD_TESTING=OFF \
      -D BUILD_DOCS=OFF \
      -D BUILD_EXAMPLES=OFF \
      -D BUILD_TESTS=OFF \
      -D OPENCL_SDK_BUILD_SAMPLES=ON \
      -D OPENCL_SDK_TEST_SAMPLES=OFF \
      -D CMAKE_TOOLCHAIN_FILE=/vcpkg/install/root/scripts/buildsystems/vcpkg.cmake  \
      -D VCPKG_TARGET_TRIPLET=x64-windows  \
      -B ./OpenCL-SDK/build -S ./OpenCL-SDK
      cmake --build ./OpenCL-SDK/build --target install
EOF
        )
        ->withConfigure(
            <<<EOF
            autoreconf -ivf
            ./configure --help

            LDFLAGS="-static " \
            ./configure --prefix={$opencl_prefix} \
            --enable-legacy \
            --enable-strict-compilation

EOF
        )
        ->withPkgName('opencv');

    $p->addLibrary($lib);
}

function install_boost(Preprocessor $p)
{
    $boost_prefix = BOOST_PREFIX;
    $lib = new Library('boost');
    $lib->withHomePage('https://www.boost.org/')
        ->withLicense('https://www.boost.org/users/license.html', Library::LICENSE_SPEC)
        ->withUrl('https://boostorg.jfrog.io/artifactory/main/release/1.81.0/source/boost_1_81_0.tar.gz')
        ->withManual('https://www.boost.org/doc/libs/1_81_0/more/getting_started/index.html')
        ->withManual('https://github.com/boostorg/wiki/wiki/')
        ->withManual('https://github.com/boostorg/wiki/wiki/Getting-Started%3A-Overview')
        ->withManual('https://www.boost.org/build/')
        ->withManual('https://www.boost.org/build/doc/html/index.html')
        ->withPrefix($boost_prefix)
        ->withCleanBuildDirectory()
        ->withCleanPreInstallDirectory($boost_prefix)
        ->withBuildScript(
            <<<EOF
            export PATH=\$SYSTEM_ORIGIN_PATH
            export PKG_CONFIG_PATH=\$SYSTEM_ORIGIN_PKG_CONFIG_PATH
            ./bootstrap.sh
            ./b2 headers
            ./b2 --release install --prefix={$boost_prefix}

            export PATH=\$SWOOLE_CLI_PATH
            export PKG_CONFIG_PATH=\$SWOOLE_CLI_PKG_CONFIG_PATH
EOF
        )
        ->withPkgName('boost');

    $p->addLibrary($lib);
}
